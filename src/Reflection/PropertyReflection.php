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
use Azirax\Documentation\Reflection\Interfaces\ModifierInterface;
use Azirax\Documentation\Reflection\Interfaces\PropertyReflectionInterface;
use Azirax\Documentation\Reflection\Traits\ClassTrait;
use Azirax\Documentation\Reflection\Traits\DocumentationTrait;
use Azirax\Documentation\Reflection\Traits\HintsTrait;
use Azirax\Documentation\Reflection\Traits\ModifierTrait;
use Azirax\Documentation\Reflection\Traits\TagsTrait;

/**
 * Reflection for class properties.
 *
 * @package      Azirax\Documentation\Reflection
 * @author       Rene Dziuba <php.tux@web.de>
 * @author       Fabien Potencier <fabien@symfony.com>
 * @copyright    Copyright (c) 2025 The Authors
 * @license      <https://opensource.org/license/mit> MIT License
 */
class PropertyReflection extends Reflection implements PropertyReflectionInterface
{
    use ClassTrait;
    use DocumentationTrait;
    use HintsTrait;
    use ModifierTrait {
        setModifier as protected traitSetModifier;
    }
    use TagsTrait;

    /**
     * Default value
     *
     * @var mixed
     */
    protected mixed $default = null;

    /**
     * Write-only flag
     *
     * @var bool
     */
    protected bool $isWriteOnly = false;

    /**
     * Magic method to string - returns the class and the property name.
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->getClass()->getName() . '::$' . $this->name;
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
        $property            = new self($array['name'], $array['line']);
        $property->shortDesc = $array['shortDesc'];
        $property->longDesc  = $array['longDesc'];
        $property->hint      = $array['hint'];
        $property->hintDesc  = $array['hintDesc'];
        $property->tags      = $array['tags'];
        $property->modifier  = $array['modifiers'];
        $property->default   = $array['default'];
        $property->errors    = $array['errors'];

        if (isset($array['isReadOnly'])) {
            $property->setReadOnly($array['isReadOnly']);
        }

        if (isset($array['isWriteOnly'])) {
            $property->setWriteOnly($array['isWriteOnly']);
        }

        if (isset($array['isIntersectionType'])) {
            $property->setIntersectionType($array['isIntersectionType']);
        }

        return $property;
    }

    /**
     * Return the default value.
     *
     * @return mixed
     */
    public function getDefault(): mixed
    {
        return $this->default;
    }

    /**
     * Check, if the property writeable.
     *
     * @return bool
     */
    public function isWriteOnly(): bool
    {
        return $this->isWriteOnly;
    }

    /**
     * Set the default value.
     *
     * @param mixed $default Default value
     *
     * @return void
     */
    public function setDefault(mixed $default): void
    {
        $this->default = $default;
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
        // if no modifiers, property is public
        if (0 === ($flag & ModifierInterface::VISIBILITY_MASK)) {
            $flag |= ModifierInterface::PUBLIC;
        }

        $this->traitSetModifier($flag);
    }

    /**
     * Mark the property as writeable or not.
     *
     * @param bool $isWriteOnly Flag
     *
     * @return void
     */
    public function setWriteOnly(bool $isWriteOnly): void
    {
        $this->isWriteOnly = $isWriteOnly;
    }

    /**
     * Returns the reflection data to array.
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'name'               => $this->name,
            'line'               => $this->line,
            'shortDesc'          => $this->shortDesc,
            'longDesc'           => $this->longDesc,
            'hint'               => $this->hint,
            'hintDesc'           => $this->hintDesc,
            'tags'               => $this->tags,
            'modifiers'          => $this->modifier,
            'default'            => $this->default,
            'errors'             => $this->errors,
            'isReadOnly'         => $this->isReadOnly(),
            'isWriteOnly'        => $this->isWriteOnly(),
            'isIntersectionType' => $this->isIntersectionType(),
        ];
    }
}
