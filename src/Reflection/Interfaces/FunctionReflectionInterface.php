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
 * Interface for the class `FunctionReflection`.
 *
 * @package      Azirax\Documentation\Reflection\Interfaces
 * @author       Rene Dziuba <php.tux@web.de>
 * @author       Fabien Potencier <fabien@symfony.com>
 * @copyright    Copyright (c) 2025 The Authors
 * @license      <https://opensource.org/license/mit> MIT License
 */
interface FunctionReflectionInterface extends ArrayInterface, HintsInterface, ModifierInterface, ReflectionInterface
{
    /**
     * Magic method to string - returns the function name.
     *
     * @return string
     */
    public function __toString(): string;

    /**
     * Add a parameter.
     *
     * @param ParameterReflectionInterface $parameter Parameter reflection object
     *
     * @return void
     */
    public function addParameter(ParameterReflectionInterface $parameter): void;

    /**
     * Returns the filename.
     *
     * @return string|null
     */
    public function getFile(): ?string;

    /**
     * Returns the namespace.
     *
     * @return string|null
     */
    public function getNamespace(): ?string;

    /**
     * Returns a method parameter.
     *
     * @param string $name Parameter name
     *
     * @return ParameterReflectionInterface|null
     */
    public function getParameter(string $name): ?ParameterReflectionInterface;

    /**
     * Returns all method parameters as an array.
     *
     * @return array
     */
    public function getParameters(): array;

    /**
     * Returns the relative file path.
     *
     * @return string|null
     */
    public function getRelativeFilePath(): ?string;

    /**
     * Returns the function source path.
     *
     * @return string
     */
    public function getSourcePath(): string;

    /**
     * Check, if the method marks as `by reference`.
     *
     * @return bool
     */
    public function isByRef(): bool;

    /**
     * Function data loaded from cache?
     *
     * @return bool
     */
    public function isFromCache(): bool;

    /**
     * Mark the method as `by reference` or not.
     *
     * @param bool $flag Flag
     *
     * @return void
     */
    public function setByRef(bool $flag): void;

    /**
     * Set the filename.
     *
     * @param string $file Filename
     *
     * @return void
     */
    public function setFile(string $file): void;

    /**
     * Set the namespace.
     *
     * @param string $namespace Namespace name
     *
     * @return void
     */
    public function setNamespace(string $namespace): void;

    /**
     * Set the method parameters as an array.
     *
     * @param array $parameters Array with method parameters
     *
     * @return void
     */
    public function setParameters(array $parameters): void;

    /**
     * Set the relative file path.
     *
     * @param string $path Path
     *
     * @return void
     */
    public function setRelativeFilePath(string $path): void;
}
