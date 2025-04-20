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

namespace Azirax\Documentation\Parser\Filters;

use Azirax\Documentation\Reflection\Interfaces\ClassReflectionInterface;
use Azirax\Documentation\Reflection\Interfaces\MethodReflectionInterface;
use Azirax\Documentation\Reflection\Interfaces\PropertyReflectionInterface;

/**
 * Default filter for classes, methods, and properties.
 *
 * Accept:
 *  - all classes
 *  - all methods
 *  - all properties
 *
 * @package      Azirax\Documentation\Parser\Filters
 * @author       Rene Dziuba <php.tux@web.de>
 * @author       Fabien Potencier <fabien@symfony.com>
 * @copyright    Copyright (c) 2025 The Authors
 * @license      <https://opensource.org/license/mit> MIT License
 */
class AllFilter implements FilterInterface
{
    /**
     * Filter for classes.
     *
     * @param ClassReflectionInterface $class Class reflection object
     *
     * @return bool
     */
    public function acceptClass(ClassReflectionInterface $class): bool
    {
        return true;
    }

    /**
     * Filter for methods.
     *
     * @param MethodReflectionInterface $method Method reflection object
     *
     * @return bool
     */
    public function acceptMethod(MethodReflectionInterface $method): bool
    {
        return true;
    }

    /**
     * Filter for properties.
     *
     * @param PropertyReflectionInterface $property Property reflection object
     *
     * @return bool
     */
    public function acceptProperty(PropertyReflectionInterface $property): bool
    {
        return true;
    }

}
