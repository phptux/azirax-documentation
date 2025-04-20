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
use Azirax\Documentation\Reflection\Interfaces\ConstantsReflectionInterface;
use Azirax\Documentation\Reflection\Traits\ClassTrait;
use Azirax\Documentation\Reflection\Traits\DocumentationTrait;
use Azirax\Documentation\Reflection\Traits\HintsTrait;
use Azirax\Documentation\Reflection\Traits\ModifierTrait;
use Azirax\Documentation\Reflection\Traits\TagsTrait;

/**
 * Reflection for class constants.
 *
 * @package      Azirax\Documentation\Reflection
 * @author       Rene Dziuba <php.tux@web.de>
 * @author       Fabien Potencier <fabien@symfony.com>
 * @copyright    Copyright (c) 2025 The Authors
 * @license      <https://opensource.org/license/mit> MIT License
 */
class ConstantsReflection extends Reflection implements ConstantsReflectionInterface
{
    use ClassTrait;
    use DocumentationTrait;
    use HintsTrait;
    use ModifierTrait;
    use TagsTrait;

    /**
     * Constant value.
     *
     * @var mixed
     */
    protected mixed $value = null;

    /**
     * Magic method to string - returns the class and the constant name.
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->getClass()->getName() . '::' . $this->name;
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
        $constant            = new self($array['name'], $array['line']);
        $constant->shortDesc = $array['shortDesc'];
        $constant->longDesc  = $array['longDesc'];
        $constant->modifier  = $array['modifiers'] ?? 0;
        $constant->tags      = $array['tags'] ?? [];
        $constant->hint      = $array['hint'] ?? null;
        $constant->value     = $array['value'] ?? null;

        return $constant;
    }

    /**
     * Returns the constant value.
     *
     * @return mixed
     */
    public function getValue(): mixed
    {
        return $this->value;
    }

    /**
     * Set the constant value.
     *
     * @param mixed $value Value
     *
     * @return void
     */
    public function setValue(mixed $value): void
    {
        $this->value = $value;
    }

    /**
     * Returns the reflection data to array.
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'name'      => $this->name,
            'line'      => $this->line,
            'shortDesc' => $this->shortDesc,
            'longDesc'  => $this->longDesc,
            'modifiers' => $this->modifier,
            'hint'      => $this->hint,
            'tags'      => $this->tags,
            'value'     => $this->value,
        ];
    }
}
