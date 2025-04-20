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
use Azirax\Documentation\Parser\Filters\AllFilter;
use Azirax\Documentation\Parser\Filters\DefaultFilter;
use Azirax\Documentation\Parser\Filters\PublicFilter;

/**
 * Service provider register the service `filter`.
 *
 * @package      Azirax\Documentation\Providers
 * @author       Rene Dziuba <php.tux@web.de>
 * @author       Fabien Potencier <fabien@symfony.com>
 * @copyright    Copyright (c) 2025 The Authors
 * @license      <https://opensource.org/license/mit> MIT License
 */
class FilterProvider implements ServiceProviderInterface
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
            'filter',
            function () use ($azirax) {
                switch ($azirax->getConfig('parseFilter', 1)) {
                    case 0:
                        // All classes, methods and all properties
                        return new AllFilter();

                    case 1:
                    default:
                        //  All classes, not private methods and not private properties
                        return new DefaultFilter();

                    case 2:
                        //  All classes, only public methods and only public properties
                        return new PublicFilter();
                }
            },
        );
    }

}
