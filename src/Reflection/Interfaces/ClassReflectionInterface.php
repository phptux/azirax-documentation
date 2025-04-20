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
 * Interface for the `ClassReflection` class.
 *
 * @package      Azirax\Documentation\Reflection\Interfaces
 * @author       Rene Dziuba <php.tux@web.de>
 * @author       Fabien Potencier <fabien@symfony.com>
 * @copyright    Copyright (c) 2025 The Authors
 * @license      <https://opensource.org/license/mit> MIT License
 */
interface ClassReflectionInterface extends ArrayInterface, DocumentationInterface, HintsInterface, InjectClassInterface, ModifierInterface, ReflectionInterface
{
    /**
     * Category: `class`
     */
    public const int CATEGORY_CLASS = 1;

    /**
     * Category: `enum`
     */
    public const int CATEGORY_ENUM      = 4;

    /**
     * Category: `interface`
     */
    public const int CATEGORY_INTERFACE = 2;

    /**
     * Category: `trait`
     */
    public const int CATEGORY_TRAIT     = 3;

    /**
     * Magic method to string - returns the class name.
     *
     * @return string
     */
    public function __toString(): string;

    /**
     * Add class constants.
     *
     * @param ConstantsReflectionInterface $constant Constant reflection object
     *
     * @return void
     */
    public function addConstant(ConstantsReflectionInterface $constant): void;

    /**
     * Add an interface to the class.
     *
     * @param string $interface Interface name
     *
     * @return void
     */
    public function addInterface(string $interface): void;

    /**
     * Add class method.
     *
     * @param MethodReflectionInterface $method Method reflection object
     *
     * @return void
     */
    public function addMethod(MethodReflectionInterface $method): void;

    /**
     * Add a class property.
     *
     * @param PropertyReflectionInterface $property Property reflection object
     *
     * @return void
     */
    public function addProperty(PropertyReflectionInterface $property): void;

    /**
     * Add a trait to the class.
     *
     * @param string $trait Trait name
     *
     * @return void
     */
    public function addTrait(string $trait): void;

    /**
     * Returns the class aliases.
     *
     * @return array
     */
    public function getAliases(): array;

    /**
     * Returns class category ID.
     *
     * @return int  Interface constants `CATEGORY_*`
     */
    public function getCategoryId(): int;

    /**
     * Returns the category name (`class`, `interface`, `trait` or `enum`)
     *
     * @return string
     */
    public function getCategoryName(): string;

    /**
     * Returns the class constants.
     *
     * @param bool $deep If `TRUE`, include the constants from interfaces and parent class
     *
     * @return ConstantsReflectionInterface[]
     */
    public function getConstants(bool $deep = false): array;

    /**
     * Returns the filename.
     *
     * @return string|null
     */
    public function getFile(): ?string;

    /**
     * Returns the class hash.
     *
     * @return string|null
     */
    public function getHash(): ?string;

    /**
     * Returns all interfaces from the class.
     *
     * @param bool $deep If `TRUE`, include the interfaces from interfaces and parent class and his interfaces
     *
     * @return array
     */
    public function getInterfaces(bool $deep = false): array;

    /**
     * Returns a class method.
     *
     * @param string $name Method name
     *
     * @return MethodReflectionInterface|null
     */
    public function getMethod(string $name): ?MethodReflectionInterface;

    /**
     * Returns all class methods.
     *
     * @param bool $deep If `TRUE`, include the methods from interfaces, traits and parent class
     *
     * @return MethodReflectionInterface[]
     */
    public function getMethods(bool $deep = false): array;

    /**
     * Returns the data from the documentation tag `mixins`.
     *
     * @return ClassReflectionInterface[]
     */
    public function getMixins(): array;

    /**
     * Returns the namespace.
     *
     * @return string|null
     */
    public function getNamespace(): ?string;

    /**
     * Returns the parent class.
     *
     * @param bool $deep If `TRUE`, include the parent class from the parent class
     *
     * @return array|ClassReflectionInterface|null
     */
    public function getParent(bool $deep = false): array|ClassReflectionInterface|null;

    /**
     * Returns a parent method.
     *
     * Method from interfaces or from the parent class.
     *
     * @param string $name Method name
     *
     * @return MethodReflectionInterface|null
     */
    public function getParentMethod(string $name): ?MethodReflectionInterface;

    /**
     * Returns the class properties.
     *
     * @param bool $deep If `TRUE`, include the properties from traits and parent class
     *
     * @return PropertyReflectionInterface[]
     */
    public function getProperties(bool $deep = false): array;

    /**
     * Returns the relative file path.
     *
     * @return string|null
     */
    public function getRelativeFilePath(): ?string;

    /**
     * Returns the short name - without a namespace.
     *
     * @return string
     */
    public function getShortName(): string;

    /**
     * Returns the function source path.
     *
     * @param int|null $line    Line number
     *
     * @return string
     */
    public function getSourcePath(?int $line = null): string;

    /**
     * Returns all traits from the class.
     *
     * @param bool $deep If `TRUE`, include the traits from traits and parent class
     *
     * @return ClassReflectionInterface[]
     */
    public function getTraits(bool $deep = false): array;

    /**
     * Check of data from the documentation tag `mixins`.
     *
     * @return bool
     */
    public function hasMixins(): bool;

    /**
     * Check, if they class a category `class`.
     *
     * @return bool
     */
    public function isClass(): bool;

    /**
     * Check, if they class a category `enum`.
     *
     * @return bool
     */
    public function isEnum(): bool;

    /**
     * Check, if class is an exception class.
     *
     * @return bool
     */
    public function isException(): bool;

    /**
     * Class data loaded from cache?
     *
     * @return bool
     */
    public function isFromCache(): bool;

    /**
     * Check, if they class a category `interface`.
     *
     * @return bool
     */
    public function isInterface(): bool;

    /**
     * Check, if they class an Phalcon Framework class.
     *
     * @return bool
     */
    public function isPhalconClass(): bool;

    /**
     * Check, if they class an internal PHP class.
     *
     * @return bool
     */
    public function isPhpClass(): bool;

    /**
     * Check, if the class from project.
     *
     * @return bool
     */
    public function isProjectClass(): bool;

    /**
     * Check, if they class a category `trait`.
     *
     * @return bool
     */
    public function isTrait(): bool;

    /**
     * Mark the class as category `class`.
     *
     * @return void
     */
    public function makeClass(): void;

    /**
     * Mark the class as category `enum`.
     *
     * @return void
     */
    public function makeEnum(): void;

    /**
     * Mark the class as category `interface`.
     *
     * @return void
     */
    public function makeInterface(): void;

    /**
     * Mark the class as category `trait`.
     *
     * @return void
     */
    public function makeTrait(): void;

    /**
     * Set the flag, if the class data not loaded from cache.
     *
     * @return void
     */
    public function notFromCache(): void;

    /**
     * Set the class aliases.
     *
     * @param array $aliases Array with class aliases
     *
     * @return void
     */
    public function setAliases(array $aliases): void;

    /**
     * Set the class category.
     *
     * @param int $category Category-ID (`ClassReflectionInterface::CATEGORY_*`)
     *
     * @return void
     */
    public function setCategory(int $category): void;

    /**
     * Set class constants.
     *
     * @param ConstantsReflectionInterface[] $constants Array with class constants
     *
     * @return void
     */
    public function setConstants(array $constants): void;

    /**
     * Set the filename.
     *
     * @param string $file Filename
     *
     * @return void
     */
    public function setFile(string $file): void;

    /**
     * Set the class hash.
     *
     * @param string $hash Hash string
     *
     * @return void
     */
    public function setHash(string $hash): void;

    /**
     * Set class interfaces.
     *
     * @param array $interfaces Interfaces
     *
     * @return void
     */
    public function setInterfaces(array $interfaces): void;

    /**
     * Set class methods.
     *
     * @param MethodReflectionInterface[] $methods Methods
     *
     * @return void
     */
    public function setMethods(array $methods): void;

    /**
     * Set the namespace.
     *
     * @param string $namespace Namespace name
     *
     * @return void
     */
    public function setNamespace(string $namespace): void;

    /**
     * Set the parent class.
     *
     * @param string $parent Parent class
     *
     * @return void
     */
    public function setParent(string $parent): void;

    /**
     * Set class properties.
     *
     * @param PropertyReflectionInterface[] $properties Array with class properties
     *
     * @return void
     */
    public function setProperties(array $properties): void;

    /**
     * Set the relative file path.
     *
     * @param string $path Path
     *
     * @return void
     */
    public function setRelativeFilePath(string $path): void;

    /**
     * Set class traits.
     *
     * @param ClassReflectionInterface[] $traits Traits
     *
     * @return void
     */
    public function setTraits(array $traits): void;

    /**
     * Sort the class interfaces.
     *
     * @param callable|null $sort Callback function for sort
     *
     * @return void
     */
    public function sortInterfaces(?callable $sort = null): void;

    /**
     * Sort the class traits.
     *
     * @param callable|null $sort Callback function for sort
     *
     * @return void
     */
    public function sortTraits(?callable $sort = null): void;
}
