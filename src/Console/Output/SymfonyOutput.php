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

namespace Azirax\Documentation\Console\Output;

use CodeLts\CliTools\Output;
use CodeLts\CliTools\OutputStyle;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Symfony output class.
 *
 * @package      Azirax\Documentation\Console\Output
 * @author       Rene Dziuba <php.tux@web.de>
 * @author       Fabien Potencier <fabien@symfony.com>
 * @copyright    Copyright (c) 2025 The Authors
 * @license      <https://opensource.org/license/mit> MIT License
 */
class SymfonyOutput implements Output
{
    /**
     * Output style object
     *
     * @var OutputStyle
     */
    private OutputStyle $style;

    /**
     * Symfony output object
     *
     * @var OutputInterface
     */
    private OutputInterface $symfonyOutput;

    /**
     * Constructor for SymfonyOutput
     *
     * @param OutputInterface $symfonyOutput Symfony output object
     * @param OutputStyle     $style         Output style object
     */
    public function __construct(OutputInterface $symfonyOutput, OutputStyle $style)
    {
        $this->symfonyOutput = $symfonyOutput;
        $this->style         = $style;
    }

    /**
     * Returns the output style object.
     *
     * @return OutputStyle
     */
    public function getStyle(): OutputStyle
    {
        return $this->style;
    }

    /**
     * Check, if debugging enabled?
     *
     * @return bool
     */
    public function isDebug(): bool
    {
        return $this->symfonyOutput->isDebug();
    }

    /**
     * Check, if the output decorated?
     *
     * @return bool
     */
    public function isDecorated(): bool
    {
        return $this->symfonyOutput->isDecorated();
    }

    /**
     * Check, if verbose enabled.
     *
     * @return bool
     */
    public function isVerbose(): bool
    {
        return $this->symfonyOutput->isVerbose();
    }

    /**
     * Write a formatted message.
     *
     * @param string $message   Message text
     *
     * @return void
     */
    public function writeFormatted(string $message): void
    {
        $this->symfonyOutput->write($message, false, OutputInterface::OUTPUT_NORMAL);
    }

    /**
     * Write a formatted message as line.
     *
     * @param string $message   Message text
     *
     * @return void
     */
    public function writeLineFormatted(string $message): void
    {
        $this->symfonyOutput->writeln($message, OutputInterface::OUTPUT_NORMAL);
    }

    /**
     * Write an unformatted message.
     *
     * @param string $message   Message text
     *
     * @return void
     */
    public function writeRaw(string $message): void
    {
        $this->symfonyOutput->write($message, false, OutputInterface::OUTPUT_RAW);
    }

}
