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

namespace Azirax\Documentation\Providers;

use Azirax\Documentation\Azirax;
use PhpParser\PrettyPrinter\Standard;

/**
 * Service provider register the service `prettyPrinter`.
 *
 * @package      Azirax\Documentation\Providers
 * @author       Rene Dziuba <php.tux@web.de>
 * @author       Fabien Potencier <fabien@symfony.com>
 * @copyright    Copyright (c) 2025 The Authors
 * @license      <https://opensource.org/license/mit> MIT License
 */
class PrettyPrinterProvider implements ServiceProviderInterface
{
    /**
     * Register the service.
     *
     * @param Azirax $azirax Azirax object
     *
     * @return void
     */
    public function register(Azirax $azirax): void
    {
        $azirax->addService(
            'prettyPrinter',
            function () {
                return new Standard();
            }
        );
    }

}
