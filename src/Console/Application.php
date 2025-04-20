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

namespace Azirax\Documentation\Console;

use Azirax\Documentation\Azirax;
use Azirax\Documentation\Console\Command\ParseCommand;
use Azirax\Documentation\Console\Command\RenderCommand;
use Azirax\Documentation\Console\Command\UpdateCommand;
use Azirax\Documentation\Console\Command\VersionCommand;
use Azirax\Documentation\ErrorHandler;
use Symfony\Component\Console\Application as BaseApplication;

use function error_reporting;

use const E_ALL;

/**
 * The console application class.
 *
 * @package      Azirax\Documantation\Console
 * @author       Rene Dziuba <php.tux@web.de>
 * @author       Fabien Potencier <fabien@symfony.com>
 * @copyright    Copyright (c) 2025 The Authors
 * @license      <https://opensource.org/license/mit> MIT License
 */
class Application extends BaseApplication
{
    /**
     * Constructor for Application
     */
    public function __construct()
    {
        // Error handling
        error_reporting(E_ALL);
        ErrorHandler::register();

        parent::__construct('Azirax documentation', Azirax::getVersion());

        // Add commands
        $this->add(new ParseCommand());
        $this->add(new RenderCommand());
        $this->add(new UpdateCommand());
        $this->add(new VersionCommand());
    }

    /**
     * Returns the long version text.
     *
     * @return string
     */
    public function getLongVersion(): string
    {
        return parent::getLongVersion() . ' by <comment>Fabien Potencier and Rene Dziuba</comment>';
    }
}
