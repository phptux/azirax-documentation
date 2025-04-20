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

/**
 * Interface for the `ConstantsReflection` class.
 *
 * @package      Azirax\Documentation\Reflection\Interfaces
 * @author       Rene Dziuba <php.tux@web.de>
 * @author       Fabien Potencier <fabien@symfony.com>
 * @copyright    Copyright (c) 2025 The Authors
 * @license      <https://opensource.org/license/mit> MIT License
 */
interface ConstantsReflectionInterface extends ArrayInterface, DocumentationInterface, HintsInterface, InjectClassInterface, ModifierInterface, ReflectionInterface
{
    /**
     * Magic method to string - returns the class and the constant name.
     *
     * @return string
     */
    public function __toString(): string;

    /**
     * Returns the constant value.
     *
     * @return mixed
     */
    public function getValue(): mixed;

    /**
     * Set the constant value.
     *
     * @param mixed $value Value
     *
     * @return void
     */
    public function setValue(mixed $value): void;

}
