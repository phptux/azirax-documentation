<?php

/*
 +------------------------------------------------------------------------+
 | Copyright (c) 2025 Azirax Team                                         |
 +------------------------------------------------------------------------+
 | This source file is subject to the MIT that is bundled     			  |
 | with this package in the file LICENSE.txt.                             |
 |                                                                        |
 | <https://opensource.org/license/mit> MIT License                       |
 +------------------------------------------------------------------------+
 | Authors: Rene Dziuba <php.tux@web.de>                                  |
 |			Fabien Potencier <fabien@symfony.com>						  |
 +------------------------------------------------------------------------+
*/
declare(strict_types = 1);

namespace Azirax\Documentation\Console\Command;

use Azirax\Documentation\Azirax;
use Azirax\Documentation\Console\Output\SymfonyOutput;
use Azirax\Documentation\Message;
use Azirax\Documentation\Parser\ParserError;
use Azirax\Documentation\Parser\Transaction;
use Azirax\Documentation\Project;
use Azirax\Documentation\Reflection\Interfaces\ClassReflectionInterface;
use Azirax\Documentation\Renderer\Diff;
use Azirax\Documentation\Version\Version;
use CodeLts\CliTools\AnalysisResult;
use CodeLts\CliTools\AnsiEscapeSequences;
use CodeLts\CliTools\ErrorsConsoleStyle;
use CodeLts\CliTools\Exceptions\FormatNotFoundException;
use CodeLts\CliTools\OutputFormat;
use CodeLts\CliTools\Symfony\SymfonyStyle;
use InvalidArgumentException;
use RuntimeException;
use Symfony\Component\Console\Command\Command as BaseCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\ConsoleOutputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;

use function array_merge;
use function count;
use function getcwd;
use function is_file;
use function sprintf;

/**
 * Abstract command class.
 *
 * @package      Azirax\Documentation\Console\Command
 * @author       Rene Dziuba <php.tux@web.de>
 * @author       Fabien Potencier <fabien@symfony.com>
 * @copyright    Copyright (c) 2025 The Authors
 * @license      <https://opensource.org/license/mit> MIT License
 */
abstract class Command extends BaseCommand
{
    /**
     * Parser error
     */
    private const int PARSE_ERROR = 64;

    /**
     * Azirax object
     *
     * @var Azirax|null
     */
    protected ?Azirax $azirax = null;

    /**
     * Array with different objects
     *
     * @var Diff[]
     */
    protected array $diffs = [];

    /**
     * Error output object.
     *
     * @var SymfonyOutput|null
     */
    protected ?SymfonyOutput $errorOutput = null;

    /**
     * Array with parser errors.
     *
     * @var ParserError[]
     */
    protected array $errors = [];

    /**
     * Input object.
     *
     * @var InputInterface|null
     */
    protected ?InputInterface $input = null;

    /**
     * Output object
     *
     * @var SymfonyOutput|null
     */
    protected ?SymfonyOutput $output = null;

    /**
     * Progress started?
     *
     * @var bool
     */
    protected bool $progressStarted = false;

    /**
     * Source root directory.
     *
     * @var string|null
     */
    protected ?string $sourceRootDirectory = null;

    /**
     * Command startet?
     *
     * @var bool
     */
    protected bool $started = false;

    /**
     * Array with parser transactions
     *
     * @var Transaction[]
     */
    protected array $transactions = [];

    /**
     * API version object or version as string
     *
     * @var Version|string|null
     */
    protected Version|string|null $version = null;

    /**
     * Display the new version.
     *
     * @return void
     */
    public function displayNewVersion(): void
    {
        $this->output->getStyle()->section(sprintf("\n<fg=cyan>Version %s</>", $this->version));
    }

    /**
     * Display the parser end.
     *
     * @param Transaction $transaction Transaction object
     *
     * @return void
     */
    public function displayParseEnd(Transaction $transaction): void
    {
        if (!$this->started) {
            return;
        }

        $this->output->writeFormatted(
            $this->output->isDecorated() ? AnsiEscapeSequences::MOVE_CURSOR_UP_2 . '<info>  Parsing   done</info>'
                . AnsiEscapeSequences::ERASE_TO_LINE_END . "\n"
                . AnsiEscapeSequences::ERASE_TO_LINE_END . "\n" . AnsiEscapeSequences::MOVE_CURSOR_UP_1 : 'Parsing done' . "\n",
        );

        $isFrozenVersion = $this->version instanceof Version
            && $this->version->isFrozen()
            && !$this->input->getOption('print-frozen-errors');

        // Do not display errors for frozen versions, it makes no sense (except if the user explicitly wants it)
        if ($this->output->isVerbose() && count($this->errors) > 0 && $isFrozenVersion === false) {
            $this->output->writeLineFormatted('');
            $analysisResult = new AnalysisResult(
                $this->errors,
                [],
                [],
                [],
            );

            OutputFormat::displayUserChoiceFormat(
                $this->getOutputFormat(),
                $analysisResult,
                $this->sourceRootDirectory,
                $this->errorOutput,
            );
            $this->output->writeLineFormatted('');
        }
    }

    /**
     * Display the parser progress.
     *
     * @param ClassReflectionInterface $class Class reflection object
     */
    public function displayParseProgress(ClassReflectionInterface $class): void
    {
        if (!$this->started) {
            $this->started = true;
        }

        if ($this->progressStarted === false) {
            // This avoids to have a "Parsing" stuck before the "Parsing" in progress
            $this->output->writeRaw("\n");
            return;
        }

        if ($this->output->isDecorated()) {
            $this->output->writeRaw(AnsiEscapeSequences::MOVE_CURSOR_UP_2);
        }

        $errorsPluralText = (1 === count($this->errors) ? '' : 's');
        $this->output->writeFormatted(
            $this->output->isDecorated() ? sprintf(
                '  Parsing %s' . AnsiEscapeSequences::ERASE_TO_LINE_END . "\n          %s"
                . AnsiEscapeSequences::ERASE_TO_LINE_END . "\n",
                count($this->errors) ? ' <fg=red>' . count($this->errors) . ' error' . $errorsPluralText . '</>' : '',
                $class->getName(),
            ) : sprintf(
                'Parsing %s %s' . "\n",
                $class->getName(),
                count($this->errors) ? 'total: ' . count($this->errors) . ' error' . $errorsPluralText : '',
            ),
        );
    }

    /**
     * Display the summary for parse.
     *
     * @return void
     */
    public function displayParseSummary(): void
    {
        if (count($this->transactions) <= 0) {
            return;
        }

        $this->output->writeLineFormatted('');// Display a line break after the title
        $this->output->writeLineFormatted(
            '<bg=cyan;fg=white> Version </>  <bg=cyan;fg=white> Updated C </>  <bg=cyan;fg=white> Removed C </>',
        );

        foreach ($this->transactions as $version => $transaction) {
            $this->output->writeLineFormatted(
                sprintf(
                    '%9s  %11d  %11d',
                    $version,
                    count($transaction->getModifiedClasses()),
                    count($transaction->getRemovedClasses()),
                ),
            );
        }
        $this->output->writeLineFormatted('');
    }

    /**
     * Display the render end.
     *
     * @param Diff $diff Diff object
     *
     * @return void
     */
    public function displayRenderEnd(Diff $diff): void
    {
        if (!$this->started) {
            return;
        }

        $this->output->writeFormatted(
            $this->output->isDecorated() ? AnsiEscapeSequences::MOVE_CURSOR_UP_2
                . '<info>  Rendering done</info>'
                . AnsiEscapeSequences::ERASE_TO_LINE_END . "\n"
                . AnsiEscapeSequences::ERASE_TO_LINE_END . "\n"
                . AnsiEscapeSequences::MOVE_CURSOR_UP_1 : 'Rendering done' . "\n",
        );
        $this->output->writeLineFormatted('');
    }

    /**
     * Display the render progress.
     *
     * @param string $section Section
     * @param string $message Message
     *
     * @return void
     */
    public function displayRenderProgress(string $section, string $message): void
    {
        if (!$this->started) {
            $this->started = true;
        }

        if ($this->progressStarted === false) {
            // This avoids to have a "Rendering" stuck before the "Rendering" in progress
            $this->output->writeRaw("\n");
            return;
        }

        if ($this->output->isDecorated()) {
            $this->output->writeRaw(AnsiEscapeSequences::MOVE_CURSOR_UP_2);
        }

        $this->output->writeFormatted(
            $this->output->isDecorated() ? sprintf(
                '  Rendering '
                . AnsiEscapeSequences::ERASE_TO_LINE_END
                . "\n            <info>%s</info> %s" . AnsiEscapeSequences::ERASE_TO_LINE_END . "\n",
                $section,
                $message,
            ) : sprintf(
                'Rendering %s %s' . "\n",
                $section,
                $message,
            ),
        );
    }

    /**
     * Display the summary for render.
     *
     * @return void
     */
    public function displayRenderSummary(): void
    {
        if (count($this->diffs) <= 0) {
            return;
        }

        $this->output->writeLineFormatted('');// Display a line break after the title
        $this->output->writeLineFormatted(
            '<bg=cyan;fg=white> Version </>  <bg=cyan;fg=white> Updated C </>'
            . '  <bg=cyan;fg=white> Updated N </>  <bg=cyan;fg=white> Removed C </>'
            . '  <bg=cyan;fg=white> Removed N </>',
        );

        foreach ($this->diffs as $version => $diff) {
            $this->output->writeLineFormatted(
                sprintf(
                    '%9s  %11d  %11d  %11d  %11d',
                    $version,
                    count($diff->getModifiedClasses()),
                    count($diff->getModifiedNamespaces()),
                    count($diff->getRemovedClasses()),
                    count($diff->getRemovedNamespaces()),
                ),
            );
        }
        $this->output->writeLineFormatted('');
    }

    /**
     * Message callback.
     *
     * @param int   $message Message ID
     * @param mixed $data    Message data
     */
    public function messageCallback(int $message, mixed $data): void
    {
        switch ($message) {
            case Message::PARSE_CLASS:
                [$step, $steps, $class] = $data;
                $this->displayParseProgress($class);
                $this->makeProgress($step, $steps);
                break;

            case Message::PARSE_ERROR:
                $this->errors = array_merge($this->errors, $data);
                break;

            case Message::SWITCH_VERSION:
                $this->version = $data;
                $this->errors  = [];
                $this->started = false;
                $this->displayNewVersion();
                break;

            case Message::PARSE_VERSION_FINISHED:
                $this->transactions[(string)$this->version] = $data;
                $this->displayParseEnd($data);
                $this->endProgress();
                $this->started = false;
                break;

            case Message::RENDER_VERSION_FINISHED:
                $this->diffs[(string)$this->version] = $data;
                $this->displayRenderEnd($data);
                $this->endProgress();
                $this->started = false;
                break;

            case Message::RENDER_PROGRESS:
                [$section, $message, $step, $steps] = $data;
                $this->displayRenderProgress($section, $message);
                $this->makeProgress($step, $steps);
                break;
        }
    }

    /**
     * Parse the given project.
     *
     * @param Project $project Project object
     *
     * @return int
     */
    public function parse(Project $project): int
    {
        if (!$this->checkOptionsValues()) {
            return 1;
        }

        $project->parse([$this, 'messageCallback'], $this->input->getOption('force'));
        $this->sourceRootDirectory = $project->getSourceDir();
        $this->output->writeFormatted(
            $this->output->isDecorated() ? '<bg=cyan;fg=white> Parsing project </>' : 'Parsing project',
        );

        $this->displayParseSummary();

        return $this->getExitCode();
    }

    /**
     * Render the given project.
     *
     * @param Project $project Project obejct
     *
     * @return int
     */
    public function render(Project $project): int
    {
        if (!$this->checkOptionsValues()) {
            return 1;
        }

        $project->render([$this, 'messageCallback'], $this->input->getOption('force'));
        $this->sourceRootDirectory = $project->getSourceDir();
        $this->output->writeFormatted(
            $this->output->isDecorated() ? '<bg=cyan;fg=white> Rendering project </>' : 'Rendering project',
        );

        $this->displayRenderSummary();

        return $this->getExitCode();
    }

    /**
     * Update the given project.
     *
     * @param Project $project Project object
     *
     * @return int
     */
    public function update(Project $project): int
    {
        if (!$this->checkOptionsValues()) {
            return 1;
        }

        $this->sourceRootDirectory = $project->getSourceDir();
        $this->output->writeFormatted(
            $this->output->isDecorated() ? '<bg=cyan;fg=white> Updating project </>' : 'Updating project',
        );
        $project->update([$this, 'messageCallback'], $this->input->getOption('force'));

        $this->displayParseSummary();
        $this->displayRenderSummary();

        return $this->getExitCode();
    }

    /**
     * Add the force option.
     *
     * @return void
     */
    protected function addForceOption(): void
    {
        $this->getDefinition()->addOption(new InputOption('force', '', InputOption::VALUE_NONE, 'Forces to rebuild from scratch', null));
    }

    /**
     * Add the ignore parse error option.
     *
     * @return void
     */
    protected function addIgnoreParseErrors(): void
    {
        $this->getDefinition()->addOption(
            new InputOption('ignore-parse-errors', '', InputOption::VALUE_NONE, 'Ignores parse errors and exits 0', null),
        );
    }

    /**
     * Add the no progress option.
     *
     * @return void
     */
    protected function addNoProgressOption(): void
    {
        $this->getDefinition()->addOption(new InputOption('no-progress', '', InputOption::VALUE_NONE, 'Do not display the progress bar', null));
    }

    /**
     * Add the output format option.
     *
     * @return void
     */
    protected function addOutputFormatOption(): void
    {
        $this->getDefinition()->addOption(
            new InputOption(
                'output-format',
                '',
                InputOption::VALUE_REQUIRED,
                'The format to display errors',
                OutputFormat::OUTPUT_FORMAT_RAW_TEXT,
            ),
        );
    }

    /**
     * Add the print frozen errors option.
     *
     * @return void
     */
    protected function addPrintFrozenErrors(): void
    {
        $this->getDefinition()->addOption(
            new InputOption('print-frozen-errors', '', InputOption::VALUE_NONE, 'Enables printing errors for frozen versions', null),
        );
    }

    /**
     * Configure the command.
     *
     * @return void
     */
    protected function configure(): void
    {
        $this->getDefinition()->addArgument(new InputArgument('config', InputArgument::REQUIRED, 'The configuration file'));
        $this->getDefinition()->addOption(new InputOption('only-version', '', InputOption::VALUE_REQUIRED, 'The version to build'));
    }

    /**
     * Progress end.
     *
     * @return void
     */
    protected function endProgress(): void
    {
        if ($this->progressStarted === false) {
            return;
        }
        $this->progressStarted = false;
        $this->output->getStyle()->progressFinish();
    }

    /**
     * Initialize the command.
     *
     * @param InputInterface  $input  Input object
     * @param OutputInterface $output Output object
     *
     * @return void
     *
     * @throw InvalidArgumentException
     */
    protected function initialize(InputInterface $input, OutputInterface $output): void
    {
        $this->input = $input;
        $stdErr      = $output;

        if ($output instanceof ConsoleOutputInterface) {
            $stdErr = $output->getErrorOutput();
        }
        $errorConsoleStyle = new ErrorsConsoleStyle($this->input, $output);
        $this->output      = new SymfonyOutput($output, new SymfonyStyle($errorConsoleStyle));
        $this->errorOutput = new SymfonyOutput($stdErr, new SymfonyStyle($errorConsoleStyle));

        /** @var string|null $config */
        $config     = $input->getArgument('config');
        $filesystem = new Filesystem();

        if ($config && !$filesystem->isAbsolutePath($config)) {
            $config = getcwd() . '/' . $config;
        }

        if ($config === null || !is_file($config)) {
            throw new InvalidArgumentException(sprintf('Configuration file "%s" does not exist.', $config));
        }

        $azirax = $this->loadAzirax($config);
        if (!$azirax instanceof Azirax) {
            throw new RuntimeException(sprintf('Configuration file "%s" must return a Azirax instance.', $config));
        }
        $this->azirax = $azirax;

        if ($input->getOption('only-version')) {
            /** @var string $onlyVersionOption */
            $onlyVersionOption = $input->getOption('only-version');
            $this->azirax->setVersion((string)$onlyVersionOption);
        }
    }

    /**
     * Make progress.
     *
     * @param int $step  Step
     * @param int $steps Steps
     *
     * @return void
     */
    protected function makeProgress(int $step, int $steps): void
    {
        if ($this->progressStarted === false) {
            $this->output->getStyle()->progressStart($steps);
            $this->progressStarted = true;
        }
        $this->output->getStyle()->progressAdvance(1);
    }

    /**
     * Check the option values.
     *
     * @return bool
     */
    private function checkOptionsValues(): bool
    {
        try {
            OutputFormat::checkOutputFormatIsValid($this->getOutputFormat());
            return true;
        } catch (FormatNotFoundException $e) {
            $this->output->getStyle()->error($e->getMessage());
            return false;
        }
    }

    /**
     * Returns the exit code.
     *
     * @return int
     */
    private function getExitCode(): int
    {
        if ($this->input->getOption('ignore-parse-errors')) {
            return 0;
        }
        if (count($this->errors) > 0) {
            return self::PARSE_ERROR;
        }
        return 0;
    }

    /**
     * Returns the output format.
     *
     * @return string
     */
    private function getOutputFormat(): string
    {
        /** @var string $outputFormat */
        $outputFormat = $this->input->getOption('output-format');
        return (string)$outputFormat;
    }

    /**
     * Returns the Azirax documentation object.
     *
     * @return Azirax|mixed
     */
    private function loadAzirax(string $config): ?Azirax
    {
        return require $config;
    }
}
