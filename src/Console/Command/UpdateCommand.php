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

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Command for update the API.
 *
 * @package      Azirax\Documentation\Console\Command
 * @author       Rene Dziuba <php.tux@web.de>
 * @author       Fabien Potencier <fabien@symfony.com>
 * @copyright    Copyright (c) 2025 The Authors
 * @license      <https://opensource.org/license/mit> MIT License
 */
class UpdateCommand extends Command
{
    /**
     * Configures the current command.
     *
     * @return void
     */
    protected function configure(): void
    {
        parent::configure();

        $this->addForceOption();
        $this->addOutputFormatOption();
        $this->addNoProgressOption();
        $this->addIgnoreParseErrors();
        $this->addPrintFrozenErrors();

        $this
            ->setName('update')
            ->setDescription('Parses then renders a project')
            ->setHelp(
                <<<EOF
The <info>%command.name%</info> command parses and renders a project:

    <info>php %command.full_name% config/azirax.php</info>

This command is the same as running the parse command followed
by the render command.
EOF
            );
    }

    /**
     * Executes the current command.
     *
     * This method is not abstract because you can use this class
     * as a concrete class. In this case, instead of defining the
     * execute() method, you set the code to execute by passing
     * a Closure to the setCode() method.
     *
     * @return int 0 if everything went fine, or an exit code
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        return $this->update($this->azirax->getService('project'));
    }
}
