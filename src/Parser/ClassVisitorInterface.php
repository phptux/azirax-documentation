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

namespace Azirax\Documentation\Parser;

use Azirax\Documentation\Reflection\Interfaces\ClassReflectionInterface;

/**
 * Interface for an class visitor class.
 *
 * @package      Azirax\Documentation\Parser
 * @author       Rene Dziuba <php.tux@web.de>
 * @author       Fabien Potencier <fabien@symfony.com>
 * @copyright    Copyright (c) 2025 The Authors
 * @license      <https://opensource.org/license/mit> MIT License
 */
interface ClassVisitorInterface
{
    /**
     * Visit the class reflection object and make changes.
     *
     * @param ClassReflectionInterface $class Class reflection object
     *
     * @return bool Class data was modified or not.
     */
    public function visit(ClassReflectionInterface $class): bool;
}
