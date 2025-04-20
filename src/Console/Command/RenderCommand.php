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
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Command for render the API.
 *
 * @package      Azirax\Documentation\Console\Command
 * @author       Rene Dziuba <php.tux@web.de>
 * @author       Fabien Potencier <fabien@symfony.com>
 * @copyright    Copyright (c) 2025 The Authors
 * @license      <https://opensource.org/license/mit> MIT License
 */
class RenderCommand extends Command
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

        $defaultVersionName = Azirax::$defaultVersionName;
        $this
            ->setName('render')
            ->setDescription('Renders a project')
            ->setHelp(
                <<<EOF
The <info>%command.name%</info> command renders a project as a static set of HTML files:

    <info>php %command.full_name% render config/aziarx.php</info>

The <comment>--force</comment> option forces a rebuild (it disables the
incremental rendering algorithm):

    <info>php %command.full_name% render config/aziarx.php --force</info>

The <comment>--version</comment> option overrides the version specified
in the configuration:

    <info>php %command.full_name% render config/azirax.php --version=$defaultVersionName</info>
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
        return $this->render($this->azirax->getService('project'));
    }
}
