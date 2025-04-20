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

namespace Azirax\Documentation\Reflection\Interfaces;

use Azirax\Documentation\Reflection\ClassReflection;

/**
 * Interface for an injection ClassReflection object.
 *
 * @package      Azirax\Documentation\Reflection\Interfaces
 * @author       Rene Dziuba <php.tux@web.de>
 * @author       Fabien Potencier <fabien@symfony.com>
 * @copyright    Copyright (c) 2025 The Authors
 * @license      <https://opensource.org/license/mit> MIT License
 */
interface InjectClassInterface
{
    /**
     * Returns the class reflection object.
     *
     * @return ClassReflection|null
     */
    public function getClass(): ?ClassReflection;

    /**
     * Set the class reflection object.
     *
     * @param ClassReflection $classReflection Class reflection object
     *
     * @return void
     */
    public function setClass(ClassReflection $classReflection): void;
}
