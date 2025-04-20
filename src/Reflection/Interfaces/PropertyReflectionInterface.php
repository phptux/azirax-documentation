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
 * Interface for class `PropertyReflection`.
 *
 * @package      Azirax\Documentation\Reflection\Interfaces
 * @author       Rene Dziuba <php.tux@web.de>
 * @author       Fabien Potencier <fabien@symfony.com>
 * @copyright    Copyright (c) 2025 The Authors
 * @license      <https://opensource.org/license/mit> MIT License
 */
interface PropertyReflectionInterface extends ArrayInterface, DocumentationInterface, HintsInterface, ModifierInterface, InjectClassInterface, ReflectionInterface
{
    /**
     * Magic method to string - returns the class and the property name.
     *
     * @return string
     */
    public function __toString(): string;

    /**
     * Return the default value.
     *
     * @return mixed
     */
    public function getDefault(): mixed;

    /**
     * Check, if the property writeable.
     *
     * @return bool
     */
    public function isWriteOnly(): bool;

    /**
     * Set the default value.
     *
     * @param mixed $default Default value
     *
     * @return void
     */
    public function setDefault(mixed $default): void;

    /**
     * Mark the property as writeable or not.
     *
     * @param bool $isWriteOnly Flag
     *
     * @return void
     */
    public function setWriteOnly(bool $isWriteOnly): void;
}
