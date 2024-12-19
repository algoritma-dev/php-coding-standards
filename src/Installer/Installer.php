<?php

declare(strict_types=1);

namespace Algoritma\CodingStandards\Installer;

use Algoritma\CodingStandards\Installer\Writer\PhpCsConfigFixerWriter;
use Algoritma\CodingStandards\Installer\Writer\PhpCsConfigWriterInterface;
use Algoritma\CodingStandards\Installer\Writer\PhpstanAlgoritmaConfigWriter;
use Algoritma\CodingStandards\Installer\Writer\PhpstanConfigWriter;
use Algoritma\CodingStandards\Installer\Writer\RectorConfigWriter;
use Composer\Composer;
use Composer\Factory;
use Composer\IO\IOInterface;
use Composer\Json\JsonFile;
use Composer\Package\PackageInterface;
use Composer\Semver\Semver;

class Installer
{
    private readonly string $projectRoot;

    /**
     * @var array<string, mixed>
     */
    private array $composerDefinition;

    private JsonFile $composerJson;

    private readonly PhpCsConfigWriterInterface $phpCsWriter;

    private readonly PhpCsConfigWriterInterface $phpstanWriter;

    private readonly PhpCsConfigWriterInterface $phpstanAlgoritmaWriter;

    private readonly PhpCsConfigWriterInterface $rectorWriter;

    /**
     * @throws \RuntimeException
     * @throws \InvalidArgumentException
     */
    public function __construct(
        private readonly IOInterface $io,
        Composer $composer,
        ?string $projectRoot = null,
        ?string $composerPath = null,
        ?PhpCsConfigWriterInterface $phpCsWriter = null,
        ?PhpCsConfigWriterInterface $phpstanWriter = null,
        ?PhpCsConfigWriterInterface $phpstanAlgoritmaWriter = null,
        ?PhpCsConfigWriterInterface $rectorWriter = null,
    ) {
        // Get composer.json location
        $composerFile = $composerPath ?? Factory::getComposerFile();
        // Calculate project root from composer.json, if necessary
        $projectRootPath = $projectRoot ?: realpath(\dirname($composerFile));

        if (! $projectRootPath) {
            throw new \RuntimeException('Unable to get project root.');
        }

        $this->projectRoot = rtrim($projectRootPath, '/\\');

        // Parse the composer.json
        $this->parseComposerDefinition($composerFile);
        $this->phpCsWriter = $phpCsWriter ?: new PhpCsConfigFixerWriter();
        $this->phpstanWriter = $phpstanWriter ?: new PhpstanConfigWriter();
        $this->phpstanAlgoritmaWriter = $phpstanAlgoritmaWriter ?: new PhpstanAlgoritmaConfigWriter();
        $this->rectorWriter = $rectorWriter ?: new RectorConfigWriter();
    }

    /**
     * @throws \Exception
     */
    public function installCommands(): void
    {
        $this->io->write('<info>Setting up Algoritma Coding Standards</info>');
        $this->createPhpCsConfig();
        $this->createPhpstanConfig();
        $this->createPhpstanAlgoritmaConfig();
        $this->createRectorConfig();
        $this->requestAddComposerScripts();
        $this->composerJson->write($this->composerDefinition);
    }

    /**
     * Check if we need to do some upgrades.
     */
    public function checkUpgrade(PackageInterface $currentPackage, PackageInterface $targetPackage): void
    {
        if (! $this->io->isInteractive()) {
            $this->io->write("\n  <info>Skipping configuration upgrade due to --no-interactive flag.</info>");

            return;
        }

        if ($this->isBcBreak($currentPackage, $targetPackage) === false) {
            return;
        }

        $this->io->write("\n  <info>Writing new configuration in project root...</info>");

        $this->phpstanAlgoritmaWriter->writeConfigFile($this->projectRoot . '/phpstan-algoritma-config.php');
    }

    private function isBcBreak(PackageInterface $currentPackage, PackageInterface $targetPackage): bool
    {
        if ($targetPackage->getFullPrettyVersion() === $currentPackage->getFullPrettyVersion()) {
            return false;
        }

        $constraint = $currentPackage->getVersion();
        if (! str_starts_with($constraint, 'dev-')) {
            $constraint = '^' . $constraint;
        }

        //        return ! ($targetPackage->getVersion() && Semver::satisfies($targetPackage->getVersion(), $constraint));
        return true;
    }

    /**
     * @throws \RuntimeException
     * @throws \InvalidArgumentException
     */
    private function parseComposerDefinition(string $composerFile): void
    {
        $this->composerJson = new JsonFile($composerFile);
        /** @var array<string, mixed> $definition */
        $definition = $this->composerJson->read();
        $this->composerDefinition = $definition;
    }

    public function createPhpCsConfig(): void
    {
        $destPath = $this->projectRoot . '/.php-cs-fixer.dist.php';

        if (is_file($destPath)) {
            $this->io->write("\n  <comment>Skipping... CS config file already exists.</comment>");
            $this->io->write('  <info>Delete .php-cs-fixer.dist.php if you want to install it.</info>');

            return;
        }

        $this->phpCsWriter->writeConfigFile($this->projectRoot . '/.php-cs-fixer.dist.php', false, true);
    }

    public function createPhpstanConfig(): void
    {
        $destPath = $this->projectRoot . '/phpstan.neon';

        if (! is_file($destPath)) {
            $this->phpstanWriter->writeConfigFile($this->projectRoot . '/phpstan.neon');
        } else {
            $this->io->write("\n  <comment>Skipping... PHPStan config file already exists.</comment>");
            $this->io->write('  <info>Delete phpstan.neon if you want to install it.</info>');
        }

        $this->phpstanAlgoritmaWriter->writeConfigFile($this->projectRoot . '/phpstan-algoritma-config.php');
    }

    public function createPhpstanAlgoritmaConfig(): void
    {
        $this->phpstanAlgoritmaWriter->writeConfigFile($this->projectRoot . '/phpstan-algoritma-config.php');
    }

    public function createRectorConfig(): void
    {
        $destPath = $this->projectRoot . '/rector.php';

        if (is_file($destPath)) {
            $this->io->write("\n  <comment>Skipping... Rector config file already exists.</comment>");
            $this->io->write('  <info>Delete rector.php if you want to install it.</info>');

            return;
        }

        $this->rectorWriter->writeConfigFile($this->projectRoot . '/rector.php');
    }

    public function requestAddComposerScripts(): void
    {
        $scripts = [
            'cs-check' => 'php-cs-fixer fix --dry-run --diff',
            'cs-fix' => 'php-cs-fixer fix --diff',
            'rector-check' => 'rector process --dry-run',
            'rector-fix' => 'rector process',
            'phpstan' => 'phpstan analyze',
        ];

        $scriptsDefinition = $this->composerDefinition['scripts'] ?? [];

        if (\is_array($scriptsDefinition) && array_diff_key($scripts, $scriptsDefinition) === []) {
            $this->io->write("\n  <comment>Skipping... Scripts already exist in composer.json.</comment>");

            return;
        }

        $question = [
            sprintf(
                "  <question>%s</question>\n",
                'Do you want to add scripts to composer.json? (Y/n)',
            ),
            '  <info>It will add these scripts:</info>',
            '  - <info>cs-check</info>',
            '  - <info>cs-fix</info>',
            '  - <info>rector-check</info>',
            '  - <info>rector-fix</info>',
            '  - <info>phpstan</info>',
            'Answer: ',
        ];

        $answer = $this->io->askConfirmation(implode("\n", $question), true);

        if (! $answer) {
            return;
        }

        if (! \array_key_exists('scripts', $this->composerDefinition)) {
            $this->composerDefinition['scripts'] = [];
        }

        foreach ($scripts as $key => $command) {
            if (isset($this->composerDefinition['scripts'][$key]) && $this->composerDefinition['scripts'][$key] !== $command) {
                $this->io->write([
                    sprintf('  <error>Another script "%s" exists!</error>', $key),
                    '  If you want, you can replace it manually with:',
                    sprintf("\n  <comment>\"%s\": \"%s\"</comment>", $key, $command),
                ]);
                continue;
            }

            $this->addComposerScript($key, $command);
        }
    }

    protected function addComposerScript(string $composerCommand, string $command): void
    {
        /** @var array<string, mixed> $scripts */
        $scripts = $this->composerDefinition['scripts'] ?? [];

        $scripts[$composerCommand] = $command;

        $this->composerDefinition['scripts'] = $scripts;
    }
}
