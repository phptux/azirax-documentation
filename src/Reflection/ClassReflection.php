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

use Azirax\Documentation\Project;
use Azirax\Documentation\Reflection\Interfaces\ClassReflectionInterface;
use Azirax\Documentation\Reflection\Interfaces\ConstantsReflectionInterface;
use Azirax\Documentation\Reflection\Interfaces\MethodReflectionInterface;
use Azirax\Documentation\Reflection\Interfaces\PropertyReflectionInterface;
use Azirax\Documentation\Reflection\Traits\DocumentationTrait;
use Azirax\Documentation\Reflection\Traits\HintsTrait;
use Azirax\Documentation\Reflection\Traits\ModifierTrait;
use Azirax\Documentation\Reflection\Traits\TagsTrait;
use UnexpectedValueException;

use function array_merge;
use function is_callable;
use function ksort;
use function ltrim;
use function str_starts_with;
use function strrpos;
use function substr;
use function trim;
use function uksort;

/**
 * Reflection class for a class.
 *
 * @package      Azirax\Documentation\Reflection
 * @author       Rene Dziuba <php.tux@web.de>
 * @author       Fabien Potencier <fabien@symfony.com>
 * @copyright    Copyright (c) 2025 The Authors
 * @license      <https://opensource.org/license/mit> MIT License
 */
class ClassReflection extends Reflection implements ClassReflectionInterface
{
    use DocumentationTrait;
    use HintsTrait;
    use ModifierTrait;
    use TagsTrait;

    /**
     * Array with aliases
     *
     * @var array
     */
    protected array $aliases = [];

    /**
     * Class category
     *
     * @var int
     */
    protected int $category = self::CATEGORY_CLASS;

    /**
     * Array with category names
     *
     * @var array
     */
    private static array $categoryName
        = [
            self::CATEGORY_CLASS     => 'class',
            self::CATEGORY_INTERFACE => 'interface',
            self::CATEGORY_TRAIT     => 'trait',
            self::CATEGORY_ENUM      => 'enum',
        ];

    /**
     * Array with class constants
     *
     * @var ConstantsReflection[]
     */
    protected array $constants = [];

    /**
     * Class filename
     *
     * @var string|null
     */
    protected ?string $file = null;

    /**
     * Data from cache?
     *
     * @var bool
     */
    protected bool $fromCache = false;

    /**
     * Class hash
     *
     * @var string|null
     */
    protected ?string $hash = null;

    /**
     * Array with interfaces
     *
     * @var array
     */
    protected array $interfaces = [];

    /**
     * Array with class methods
     *
     * @var MethodReflection[]
     */
    protected array $methods = [];

    /**
     * Namespace
     *
     * @var string|null
     */
    protected ?string $namespace = null;

    /**
     * Parent class
     *
     * @var string|null
     */
    protected ?string $parent = null;

    /**
     * Project object
     *
     * @var Project|null
     */
    protected ?Project $project = null;

    /**
     * Is project class?
     *
     * @var bool
     */
    protected bool $projectClass = true;

    /**
     * Array with class properties
     *
     * @var PropertyReflection[]
     */
    protected array $properties = [];

    /**
     * Relative file path
     *
     * @var string|null
     */
    protected ?string $relativeFilePath = null;

    /**
     * Array with Traits
     *
     * @var array
     */
    protected array $traits = [];

    /**
     * Magic method to string - returns the class name.
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->name;
    }

    /**
     * Add class constants.
     *
     * @param ConstantsReflectionInterface $constant Constant reflection object
     *
     * @return void
     */
    public function addConstant(ConstantsReflectionInterface $constant): void
    {
        $this->constants[$constant->getName()] = $constant;
        $constant->setClass($this);
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
        $this->interfaces[$interface] = $interface;
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
        $this->methods[$method->getName()] = $method;
        $method->setClass($this);
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
        $this->properties[$property->getName()] = $property;
        $property->setClass($this);
    }

    /**
     * Add a trait to the class.
     *
     * @param string $trait Trait name
     *
     * @return void
     */
    public function addTrait(string $trait): void
    {
        $this->traits[$trait] = $trait;
    }

    /**
     * Create the reflection object from the given data.
     *
     * @param Project $project Project object
     * @param array   $array   Data array
     *
     * @return $this
     */
    public static function fromArray(Project $project, array $array): static
    {
        $class                   = new self($array['name'], $array['line']);
        $class->shortDesc        = $array['shortDesc'];
        $class->longDesc         = $array['longDesc'];
        $class->hint             = $array['hint'];
        $class->tags             = $array['tags'];
        $class->namespace        = $array['namespace'];
        $class->hash             = $array['hash'];
        $class->file             = $array['file'];
        $class->relativeFilePath = $array['relativeFile'];
        $class->modifier         = $array['modifiers'];
        $class->fromCache        = true;

        if (isset($array['isReadOnly'])) {
            $class->setReadOnly($array['isReadOnly']);
        }

        if ($array['isInterface']) {
            $class->makeInterface();
        }
        if ($array['isTrait']) {
            $class->makeTrait();
        }
        if ($array['isEnum']) {
            $class->makeEnum();
        }
        $class->aliases    = $array['aliases'];
        $class->errors     = $array['errors'];
        $class->parent     = $array['parent'];
        $class->interfaces = $array['interfaces'];
        $class->constants  = $array['constants'];
        $class->traits     = $array['traits'];

        $class->setProject($project);

        foreach ($array['methods'] as $method) {
            $class->addMethod(MethodReflection::fromArray($project, $method));
        }

        foreach ($array['properties'] as $property) {
            $class->addProperty(PropertyReflection::fromArray($project, $property));
        }

        foreach ($array['constants'] as $constant) {
            $class->addConstant(ConstantsReflection::fromArray($project, $constant));
        }

        return $class;
    }

    /**
     * Returns the class aliases.
     *
     * @return array
     */
    public function getAliases(): array
    {
        return $this->aliases;
    }

    /**
     * Returns class category ID.
     *
     * @return int  Interface constants (`ClassReflectionInterface::CATEGORY_*`)
     */
    public function getCategoryId(): int
    {
        return $this->category;
    }

    /**
     * Returns the category name (`class`, `interface`, `trait` or `enum`)
     *
     * @return string
     */
    public function getCategoryName(): string
    {
        return self::$categoryName[$this->category];
    }

    /**
     * Returns the class object.
     *
     * @return $this|null
     */
    public function getClass(): ?static
    {
        return $this;
    }

    /**
     * Returns the class constants.
     *
     * @param bool $deep If `TRUE`, include the constants from interfaces and parent class
     *
     * @return ConstantsReflectionInterface[]
     */
    public function getConstants(bool $deep = false): array
    {
        if (false === $deep) {
            return $this->constants;
        }

        $constants = [];

        // Parent class
        if ($this->getParent()) {
            foreach ($this->getParent()->getConstants(true) as $name => $constant) {
                $constants[$name] = $constant;
            }
        }

        // Interfaces
        foreach ($this->getInterfaces(true) as $interface) {
            foreach ($interface->getConstants(true) as $name => $constant) {
                $constants[$name] = $constant;
            }
        }

        // Class/Interface constants
        foreach ($this->constants as $name => $constant) {
            $constants[$name] = $constant;
        }

        return $constants;
    }

    /**
     * Returns the filename.
     *
     * @return string|null
     */
    public function getFile(): ?string
    {
        return $this->file;
    }

    /**
     * Returns the class hash.
     *
     * @return string|null
     */
    public function getHash(): ?string
    {
        return $this->hash;
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
        $interfaces = [];
        foreach ($this->interfaces as $interface) {
            $interfaces[] = $this->project->getClass($interface);
        }

        if (false === $deep) {
            return $interfaces;
        }

        $allInterfaces = $interfaces;
        foreach ($interfaces as $interface) {
            $allInterfaces = array_merge($allInterfaces, $interface->getInterfaces(true));
        }

        if ($parent = $this->getParent()) {
            $allInterfaces = array_merge($allInterfaces, $parent->getInterfaces(true));
        }

        return $allInterfaces;
    }

    /**
     * Returns a class method.
     *
     * @param string $name Method name
     *
     * @return MethodReflectionInterface|null
     */
    public function getMethod(string $name): ?MethodReflectionInterface
    {
        return $this->methods[$name] ?? null;
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
        if (false === $deep) {
            return $this->methods;
        }

        $methods = [];
        if ($this->isInterface()) {
            foreach ($this->getInterfaces(true) as $interface) {
                foreach ($interface->getMethods(true) as $name => $method) {
                    $methods[$name] = $method;
                }
            }
        }

        if ($this->getParent()) {
            foreach ($this->getParent()->getMethods(true) as $name => $method) {
                $methods[$name] = $method;
            }
        }

        foreach ($this->getTraits(true) as $trait) {
            foreach ($trait->getMethods(true) as $name => $method) {
                $methods[$name] = $method;
            }
        }

        foreach ($this->methods as $name => $method) {
            $methods[$name] = $method;
        }

        return $methods;
    }

    /**
     * Returns the data from the documentation tag `mixins`.
     *
     * @return ClassReflectionInterface[]
     */
    public function getMixins(): array
    {
        $mixins = [];

        foreach ($this->getTags('mixin') as $mixin) {
            $mixins[] = [
                'class' => new ClassReflection($mixin[0], -1),
            ];
        }

        return $mixins;
    }

    /**
     * Returns the namespace.
     *
     * @return string|null
     */
    public function getNamespace(): ?string
    {
        return $this->namespace;
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
        if (!$this->parent) {
            return $deep ? [] : null;
        }

        $parent = $this->project->getClass($this->parent);

        if (false === $deep) {
            return $parent;
        }

        return array_merge([$parent], $parent->getParent(true));
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
        if ($this->getParent()) {
            foreach ($this->getParent()->getMethods(true) as $n => $method) {
                if ($name == $n) {
                    return $method;
                }
            }
        }

        foreach ($this->getInterfaces(true) as $interface) {
            foreach ($interface->getMethods(true) as $n => $method) {
                if ($name == $n) {
                    return $method;
                }
            }
        }

        return null;
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
        if (false === $deep) {
            return $this->properties;
        }

        $properties = [];
        if ($this->getParent()) {
            foreach ($this->getParent()->getProperties(true) as $name => $property) {
                $properties[$name] = $property;
            }
        }

        foreach ($this->getTraits(true) as $trait) {
            foreach ($trait->getProperties(true) as $name => $property) {
                $properties[$name] = $property;
            }
        }

        foreach ($this->properties as $name => $property) {
            $properties[$name] = $property;
        }

        return $properties;
    }

    /**
     * Returns the relative file path.
     *
     * @return string|null
     */
    public function getRelativeFilePath(): ?string
    {
        return $this->relativeFilePath;
    }

    /**
     * Returns the short name - without a namespace.
     *
     * @return string
     */
    public function getShortName(): string
    {
        $pos = strrpos($this->name, '\\');
        if ($pos !== false) {
            return substr($this->name, $pos + 1);
        }

        return $this->name;
    }

    /**
     * Returns the function source path.
     *
     * @param int|null $line Line number
     *
     * @return string
     */
    public function getSourcePath(?int $line = null): string
    {
        if ($this->relativeFilePath === null) {
            return '';
        }

        return $this->project->getViewSourceUrl($this->relativeFilePath, $line);
    }

    /**
     * Returns all traits from the class.
     *
     * @param bool $deep If `TRUE`, include the traits from traits and parent class
     *
     * @return ClassReflectionInterface[]
     */
    public function getTraits(bool $deep = false): array
    {
        $traits = [];
        foreach ($this->traits as $trait) {
            $traits[] = $this->project->getClass($trait);
        }

        if (false === $deep) {
            return $traits;
        }

        $allTraits = $traits;
        foreach ($traits as $trait) {
            $allTraits = array_merge($allTraits, $trait->getTraits(true));
        }

        if ($parent = $this->getParent()) {
            $allTraits = array_merge($allTraits, $parent->getTraits(true));
        }

        return $allTraits;
    }

    /**
     * Check of data from the documentation tag `mixins`.
     *
     * @return bool
     */
    public function hasMixins(): bool
    {
        return !empty($this->getTags('mixin'));
    }

    /**
     * Check, if they class a category `class`.
     *
     * @return bool
     */
    public function isClass(): bool
    {
        return self::CATEGORY_CLASS === $this->category;
    }

    /**
     * Check, if they class a category `enum`.
     *
     * @return bool
     */
    public function isEnum(): bool
    {
        return self::CATEGORY_ENUM === $this->category;
    }

    /**
     * Check, if class is an exception class.
     *
     * @return bool
     */
    public function isException(): bool
    {
        $parent = $this;

        while ($parent = $parent->getParent()) {
            if ('Exception' === $parent->getName()) {
                return true;
            }
        }

        return false;
    }

    /**
     * Class data loaded from cache?
     *
     * @return bool
     */
    public function isFromCache(): bool
    {
        return $this->fromCache;
    }

    /**
     * Check, if they class a category `interface`.
     *
     * @return bool
     */
    public function isInterface(): bool
    {
        return self::CATEGORY_INTERFACE === $this->category;
    }

    /**
     * Check, if they class an Phalcon Framework class.
     *
     * @return bool
     */
    public function isPhalconClass(): bool
    {
        return str_starts_with(trim($this->name, '\\'), 'Phalcon\\');
    }

    /**
     * Check, if they class an internal PHP class.
     *
     * @return bool
     */
    public function isPhpClass(): bool
    {
        return Project::isPhpInternalClass($this->name);
    }

    /**
     * Check, if the class from project.
     *
     * @return bool
     */
    public function isProjectClass(): bool
    {
        return $this->projectClass;
    }

    /**
     * Check, if they class a category `trait`.
     *
     * @return bool
     */
    public function isTrait(): bool
    {
        return self::CATEGORY_TRAIT === $this->category;
    }

    /**
     * Mark the class as category `class`.
     *
     * @return void
     */
    public function makeClass(): void
    {
        $this->setCategory(self::CATEGORY_CLASS);
    }

    /**
     * Mark the class as category `enum`.
     *
     * @return void
     */
    public function makeEnum(): void
    {
        $this->setCategory(self::CATEGORY_ENUM);
    }

    /**
     * Mark the class as category `interface`.
     *
     * @return void
     */
    public function makeInterface(): void
    {
        $this->setCategory(self::CATEGORY_INTERFACE);
    }

    /**
     * Mark the class as category `trait`.
     *
     * @return void
     */
    public function makeTrait(): void
    {
        $this->setCategory(self::CATEGORY_TRAIT);
    }

    /**
     * Set the flag, if the class data not loaded from cache.
     *
     * @return void
     */
    public function notFromCache(): void
    {
        $this->fromCache = false;
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
        $this->aliases = $aliases;
    }

    /**
     * Set the class category.
     *
     * @param int $category Category-ID (`ClassReflectionInterface::CATEGORY_*`)
     *
     * @return void
     */
    public function setCategory(int $category): void
    {
        $this->category = $category;
    }

    /**
     * Set the class reflection object.
     *
     * @param ClassReflection $classReflection Class reflection object
     *
     * @return void
     */
    public function setClass(ClassReflection $classReflection): void
    {
        throw new UnexpectedValueException('Class object can not overwrite');
    }

    /**
     * Set class constants.
     *
     * @param ConstantsReflectionInterface[] $constants Array with class constants
     *
     * @return void
     */
    public function setConstants(array $constants): void
    {
        $this->constants = $constants;
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
        $this->file = $file;
    }

    /**
     * Set the class hash.
     *
     * @param string $hash Hash string
     *
     * @return void
     */
    public function setHash(string $hash): void
    {
        $this->hash = $hash;
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
        $this->interfaces = $interfaces;
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
        $this->methods = $methods;
    }

    /**
     * Set the name.
     *
     * @param string $name Name
     *
     * @return void
     */
    public function setName(string $name): void
    {
        parent::setName(ltrim($name, '\\'));
    }

    /**
     * Set the namespace.
     *
     * @param string $namespace Namespace name
     *
     * @return void
     */
    public function setNamespace(string $namespace): void
    {
        $this->namespace = ltrim($namespace, '\\');
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
        $this->parent = $parent;
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
        $this->properties = $properties;
    }

    /**
     * Set the relative file path.
     *
     * @param string $path Path
     *
     * @return void
     */
    public function setRelativeFilePath(string $path): void
    {
        $this->relativeFilePath = $path;
    }

    /**
     * Set class traits.
     *
     * @param ClassReflectionInterface[] $traits Traits
     *
     * @return void
     */
    public function setTraits(array $traits): void
    {
        $this->traits = $traits;
    }

    /**
     * Sort the class interfaces.
     *
     * @param callable|null $sort Callback function for sort
     *
     * @return void
     */
    public function sortInterfaces(?callable $sort = null): void
    {
        if (is_callable($sort)) {
            uksort($this->interfaces, $sort);
        } else {
            ksort($this->interfaces);
        }
    }

    /**
     * Sort the class traits.
     *
     * @param callable|null $sort Callback function for sort
     *
     * @return void
     */
    public function sortTraits(?callable $sort = null): void
    {
        if (is_callable($sort)) {
            uksort($this->traits, $sort);
        } else {
            ksort($this->traits);
        }
    }

    /**
     * Returns the reflection data to array.
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'name'         => $this->name,
            'line'         => $this->line,
            'shortDesc'    => $this->shortDesc,
            'longDesc'     => $this->longDesc,
            'hint'         => $this->hint,
            'tags'         => $this->tags,
            'namespace'    => $this->namespace,
            'file'         => $this->file,
            'relativeFile' => $this->relativeFilePath,
            'hash'         => $this->hash,
            'parent'       => $this->parent,
            'modifiers'    => $this->modifier,
            'isTrait'      => $this->isTrait(),
            'isEnum'       => $this->isEnum(),
            'isInterface'  => $this->isInterface(),
            'isReadOnly'   => $this->isReadOnly(),
            'aliases'      => $this->aliases,
            'errors'       => $this->errors,
            'interfaces'   => $this->interfaces,
            'traits'       => $this->traits,
            'properties'   => array_map(
                static function ($property) {
                    return $property->toArray();
                },
                $this->properties,
            ),
            'methods'      => array_map(
                static function ($method) {
                    return $method->toArray();
                },
                $this->methods,
            ),
            'constants'    => array_map(
                static function ($constant) {
                    return $constant->toArray();
                },
                $this->constants,
            ),
        ];
    }
}
