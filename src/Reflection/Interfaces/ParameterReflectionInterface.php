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
 * Interface for class `ParameterReflection`.
 *
 * @package      Azirax\Documentation\Reflection\Interfaces
 * @author       Rene Dziuba <php.tux@web.de>
 * @author       Fabien Potencier <fabien@symfony.com>
 * @copyright    Copyright (c) 2025 The Authors
 * @license      <https://opensource.org/license/mit> MIT License
 */
interface ParameterReflectionInterface extends ArrayInterface, DocumentationInterface, HintsInterface, InjectClassInterface, ModifierInterface, ReflectionInterface
{
    /**
     * Magic method to string - returns the class name.
     *
     * @return string
     */
    public function __toString(): string;

    /**
     * Returns the default value.
     *
     * @return mixed
     */
    public function getDefault(): mixed;

    /**
     * Returns the FunctionReflection object.
     *
     * @return FunctionReflectionInterface|null
     */
    public function getFunction(): ?FunctionReflectionInterface;

    /**
     * Returns the MethodReflection object.
     *
     * @return MethodReflectionInterface|null
     */
    public function getMethod(): ?MethodReflectionInterface;

    /**
     * Check, if the parameter marks as `variadic`.
     *
     * @return bool
     */
    public function getVariadic(): bool;

    /**
     * Check, if the parameter mark as `by reference`.
     *
     * @return bool
     */
    public function isByRef(): bool;

    /**
     * Mark the parameter as `by reference` or not.
     *
     * @param bool $flag Flag
     *
     * @return void
     */
    public function setByRef(bool $flag): void;

    /**
     * Set default value.
     *
     * @param mixed $default Default value
     *
     * @return void
     */
    public function setDefault(mixed $default): void;

    /**
     * Set the FunctionReflection object.
     *
     * @param FunctionReflectionInterface $function FunctionReflection object
     *
     * @return void
     */
    public function setFunction(FunctionReflectionInterface $function): void;

    /**
     * Set the MethodReflection object.
     *
     * @param MethodReflectionInterface $method MethodReflection object
     *
     * @return void
     */
    public function setMethod(MethodReflectionInterface $method): void;

    /**
     * Mark the parameter as `variadic` or not.
     *
     * @param bool $flag Flag
     *
     * @return void
     */
    public function setVariadic(bool $flag): void;
}
