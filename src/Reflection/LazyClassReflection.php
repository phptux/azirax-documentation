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

namespace Azirax\Documentation\Reflection;

use Azirax\Documentation\Reflection\Interfaces\ClassReflectionInterface;
use Azirax\Documentation\Reflection\Interfaces\MethodReflectionInterface;
use Azirax\Documentation\Reflection\Interfaces\PropertyReflectionInterface;
use Azirax\Documentation\Reflection\Traits\DocumentationTrait;
use Azirax\Documentation\Reflection\Traits\HintsTrait;
use Azirax\Documentation\Reflection\Traits\ModifierTrait;
use LogicException;
use ReflectionClass;

/**
 * Class reflection class for lazy load data.
 *
 * @package      Azirax\Documentation\Reflection
 * @author       Rene Dziuba <php.tux@web.de>
 * @author       Fabien Potencier <fabien@symfony.com>
 * @copyright    Copyright (c) 2025 The Authors
 * @license      <https://opensource.org/license/mit> MIT License
 */
class LazyClassReflection extends ClassReflection
{
    use DocumentationTrait {
        getShortDesc as traitGetShortDesc;
        getLongDesc as traitGetLongDesc;
    }
    use HintsTrait {
        getHint as traitGetHint;
    }
    use ModifierTrait {
        isFinal as traitIsFinal;
        isAbstract as traitIsAbstract;
    }

    /**
     * Flag for lazy load data
     *
     * @var bool
     */
    protected bool $loaded = false;

    /**
     * Constructor for LazyClassReflection
     *
     * @param string $name Class name
     */
    public function __construct(string $name)
    {
        parent::__construct($name, -1);
    }

    /**
     * Add an interface to the class.
     *
     * @param string $interface Interface name
     *
     * @return void
     */
    public function addInterface(string $interface): void
    {
        throw new LogicException('A LazyClassReflection instance is read-only.');
    }

    /**
     * Add class method.
     *
     * @param MethodReflectionInterface $method Method reflection object
     *
     * @return void
     */
    public function addMethod(MethodReflectionInterface $method): void
    {
        throw new LogicException('A LazyClassReflection instance is read-only.');
    }

    /**
     * Add a class property.
     *
     * @param PropertyReflectionInterface $property Property reflection object
     *
     * @return void
     */
    public function addProperty(PropertyReflectionInterface $property): void
    {
        throw new LogicException('A LazyClassReflection instance is read-only.');
    }

    /**
     * Returns the class aliases.
     *
     * @return array
     */
    public function getAliases(): array
    {
        $this->load();

        return parent::getAliases();
    }

    /**
     * Returns the filename.
     *
     * @return string|null
     */
    public function getFile(): ?string
    {
        $this->load();

        return parent::getFile();
    }

    /**
     * Returns all hints as an array.
     *
     * @return HintReflection[]
     */
    public function getHint(): array
    {
        $this->load();

        return $this->traitGetHint();
    }

    /**
     * Returns all interfaces from the class.
     *
     * @param bool $deep If `TRUE`, include the interfaces from interfaces and parent class and his interfaces
     *
     * @return array
     */
    public function getInterfaces(bool $deep = false): array
    {
        $this->load();

        return parent::getInterfaces($deep);
    }

    /**
     * Returns the long description.
     *
     * @return string|null
     */
    public function getLongDesc(): ?string
    {
        $this->load();

        return $this->traitGetLongDesc();
    }

    /**
     * Returns all class methods.
     *
     * @param bool $deep If `TRUE`, include the methods from interfaces, traits and parent class
     *
     * @return MethodReflectionInterface[]
     */
    public function getMethods(bool $deep = false): array
    {
        $this->load();

        return parent::getMethods($deep);
    }

    /**
     * Returns the parent class.
     *
     * @param bool $deep If `TRUE`, include the parent class from the parent class
     *
     * @return array|ClassReflectionInterface|null
     */
    public function getParent(bool $deep = false): array|ClassReflectionInterface|null
    {
        $this->load();

        return parent::getParent($deep);
    }

    /**
     * Returns a parent method.
     *
     * Method from interfaces or from the parent class.
     *
     * @param string $name Method name
     *
     * @return MethodReflectionInterface|null
     */
    public function getParentMethod(string $name): ?MethodReflectionInterface
    {
        $this->load();

        return parent::getParentMethod($name);
    }

    /**
     * Returns the class properties.
     *
     * @param bool $deep If `TRUE`, include the properties from traits and parent class
     *
     * @return PropertyReflectionInterface[]
     */
    public function getProperties(bool $deep = false): array
    {
        $this->load();

        return parent::getProperties($deep);
    }

    /**
     * Returns the short description.
     *
     * @return string|null
     */
    public function getShortDesc(): ?string
    {
        $this->load();

        return $this->traitGetShortDesc();
    }

    /**
     * Check, if the modifier `abstract`.
     *
     * @return bool
     */
    public function isAbstract(): bool
    {
        $this->load();

        return $this->traitIsAbstract();
    }

    /**
     * Check, if they class a category `enum`.
     *
     * @return bool
     */
    public function isEnum(): bool
    {
        $this->load();

        return parent::isEnum();
    }

    /**
     * Check, if class is an exception class.
     *
     * @return bool
     */
    public function isException(): bool
    {
        $this->load();

        return parent::isException();
    }

    /**
     * Check, if the modifier `final`.
     *
     * @return bool
     */
    public function isFinal(): bool
    {
        $this->load();

        return $this->traitIsFinal();
    }

    /**
     * Check, if they class a category `interface`.
     *
     * @return bool
     */
    public function isInterface(): bool
    {
        $this->load();

        return parent::isInterface();
    }

    /**
     * Check, if the class from project.
     *
     * @return bool
     */
    public function isProjectClass(): bool
    {
        $this->load();

        return parent::isProjectClass();
    }

    /**
     * Check, if they class a category `trait`.
     *
     * @return bool
     */
    public function isTrait(): bool
    {
        $this->load();

        return parent::isTrait();
    }

    /**
     * Set the class aliases.
     *
     * @param array $aliases Array with class aliases
     *
     * @return void
     */
    public function setAliases(array $aliases): void
    {
        throw new LogicException('A LazyClassReflection instance is read-only.');
    }

    /**
     * Set the filename.
     *
     * @param string $file Filename
     *
     * @return void
     */
    public function setFile(string $file): void
    {
        throw new LogicException('A LazyClassReflection instance is read-only.');
    }

    /**
     * Add or set hint(s).
     *
     * @param array|ClassReflectionInterface|string $hint Hint(s)
     */
    public function setHint(array|ClassReflectionInterface|string $hint): void
    {
        throw new LogicException('A LazyClassReflection instance is read-only.');
    }

    /**
     * Set class interfaces.
     *
     * @param array $interfaces Interfaces
     *
     * @return void
     */
    public function setInterfaces(array $interfaces): void
    {
        throw new LogicException('A LazyClassReflection instance is read-only.');
    }

    /**
     * Set the long description.
     *
     * @param string|null $longDesc Description text
     */
    public function setLongDesc(?string $longDesc): void
    {
        throw new LogicException('A LazyClassReflection instance is read-only.');
    }

    /**
     * Set class methods.
     *
     * @param MethodReflectionInterface[] $methods Methods
     *
     * @return void
     */
    public function setMethods(array $methods): void
    {
        throw new LogicException('A LazyClassReflection instance is read-only.');
    }

    /**
     * Set the modifier.
     *
     * @param int $flag Flag (Class constants `ModifierInterface::*`)
     *
     * @return void
     */
    public function setModifier(int $flag): void
    {
        throw new LogicException('A LazyClassReflection instance is read-only.');
    }

    /**
     * Set the parent class.
     *
     * @param string $parent Parent class
     *
     * @return void
     */
    public function setParent(string $parent): void
    {
        throw new LogicException('A LazyClassReflection instance is read-only.');
    }

    /**
     * Set class properties.
     *
     * @param PropertyReflectionInterface[] $properties Array with class properties
     *
     * @return void
     */
    public function setProperties(array $properties): void
    {
        throw new LogicException('A LazyClassReflection instance is read-only.');
    }

    /**
     * Set the short description.
     *
     * @param string|null $shortDesc Description text
     */
    public function setShortDesc(?string $shortDesc): void
    {
        throw new LogicException('A LazyClassReflection instance is read-only.');
    }

    /**
     * Load the data.
     *
     * @return void
     */
    protected function load(): void
    {
        if ($this->loaded === true) {
            return;
        }

        $class = $this->project->loadClass($this->name);

        if ($class === null) {
            $this->projectClass = false;
        } else {
            $ref = new ReflectionClass($class);

            foreach ($ref->getProperties() as $prop) {
                if (!$prop->isStatic()) {
                    $property        = $prop->getName();
                    $this->$property = $class->$property;
                }
            }
        }

        $this->loaded = true;
    }
}
