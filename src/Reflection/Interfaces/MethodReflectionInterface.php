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
 * Interface for the class `MethodReflection`.
 *
 * @package      Azirax\Documentation\Reflection\Interfaces
 * @author       Rene Dziuba <php.tux@web.de>
 * @author       Fabien Potencier <fabien@symfony.com>
 * @copyright    Copyright (c) 2025 The Authors
 * @license      <https://opensource.org/license/mit> MIT License
 */
interface MethodReflectionInterface extends ArrayInterface, DocumentationInterface, HintsInterface, InjectClassInterface, ModifierInterface, ReflectionInterface
{
    /**
     * Magic method to string - returns the class name.
     *
     * @return string
     */
    public function __toString(): string;

    /**
     * Check, if the method marks as `by reference`.
     *
     * @return bool
     */
    public function isByRef(): bool;

    /**
     * Mark the method as `by reference` or not.
     *
     * @param bool $flag Flag
     *
     * @return void
     */
    public function setByRef(bool $flag): void;

    /**
     * Add a parameter.
     *
     * @param ParameterReflectionInterface $parameter   Parameter reflection object
     *
     * @return void
     */
    public function addParameter(ParameterReflectionInterface $parameter): void;

    /**
     * Returns all method parameters as an array.
     *
     * @return array
     */
    public function getParameters(): array;

    /**
     * Returns a method parameter.
     *
     * @param string $name  Parameter name
     *
     * @return ParameterReflectionInterface|null
     */
    public function getParameter(string $name): ?ParameterReflectionInterface;

    /**
     * Set the method parameters as an array.
     *
     * @param array $parameters Array with method parameters
     *
     * @return void
     */
    public function setParameters(array $parameters): void;

    /**
     * Returns the method source path.
     *
     * @return string
     */
    public function getSourcePath(): string;
}
