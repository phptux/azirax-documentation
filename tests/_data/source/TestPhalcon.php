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

namespace Azirax\Documentation\Tests\Source;

use Phalcon\Di\DiInterface;
use Phalcon\Di\InjectionAwareInterface;

/**
 * Test class for Phalcon Framework API url.
 *
 * @package      Azirax\Documentation\Tests\Source
 * @author       Rene Dziuba <php.tux@web.de>
 * @author       Fabien Potencier <fabien@symfony.com>
 * @copyright    Copyright (c) 2025 The Authors
 * @license      <https://opensource.org/license/mit> MIT License
 */
class TestPhalcon implements InjectionAwareInterface
{
    /**
     * Phalcon's DiC object
     *
     * @var DiInterface|null
     */
    private ?DiInterface $container = null;

    /**
     * Sets the dependency injector
     *
     * @param DiInterface $container
     *
     * @return void
     */
    public function setDI(DiInterface $container): void
    {
        // TODO: Implement setDI() method.
    }

    /**
     * Returns the internal dependency injector
     *
     * @return DiInterface
     */
    public function getDI(): DiInterface
    {
        // TODO: Implement getDI() method.
    }

}
