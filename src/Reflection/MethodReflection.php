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
use Azirax\Documentation\Reflection\Interfaces\MethodReflectionInterface;
use Azirax\Documentation\Reflection\Interfaces\ModifierInterface;
use Azirax\Documentation\Reflection\Interfaces\ParameterReflectionInterface;
use Azirax\Documentation\Reflection\Traits\ClassTrait;
use Azirax\Documentation\Reflection\Traits\DocumentationTrait;
use Azirax\Documentation\Reflection\Traits\HintsTrait;
use Azirax\Documentation\Reflection\Traits\ModifierTrait;
use Azirax\Documentation\Reflection\Traits\TagsTrait;

use function array_map;
use function array_values;
use function ctype_digit;

/**
 * Reflection for a class method.
 *
 * @package      Azirax\Documentation\Reflection
 * @author       Rene Dziuba <php.tux@web.de>
 * @author       Fabien Potencier <fabien@symfony.com>
 * @copyright    Copyright (c) 2025 The Authors
 * @license      <https://opensource.org/license/mit> MIT License
 */
class MethodReflection extends Reflection implements MethodReflectionInterface
{
    use ClassTrait;
    use DocumentationTrait;
    use HintsTrait;
    use ModifierTrait {
        setModifier as traitSetModifier;
    }
    use TagsTrait;

    /**
     * Mark the method as `by reference`
     *
     * @var bool
     */
    protected bool $byRef = false;

    /**
     * Array with method parameters
     *
     * @var ParameterReflectionInterface[]
     */
    protected array $parameters = [];

    /**
     * Magic method to string - returns the class name.
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->getClass() . '::' . $this->name;
    }

    /**
     * Add a parameter.
     *
     * @param ParameterReflectionInterface $parameter Parameter reflection object
     *
     * @return void
     */
    public function addParameter(ParameterReflectionInterface $parameter): void
    {
        $this->parameters[$parameter->getName()] = $parameter;
        $parameter->setMethod($this);
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
        $method                     = new self($array['name'], $array['line']);
        $method->shortDesc          = $array['shortDesc'];
        $method->longDesc           = $array['longDesc'];
        $method->hint               = $array['hint'];
        $method->hintDesc           = $array['hintDesc'];
        $method->tags               = $array['tags'];
        $method->modifier           = $array['modifiers'];
        $method->byRef              = $array['isByRef'];
        $method->exceptions         = $array['exceptions'];
        $method->errors             = $array['errors'];
        $method->see                = $array['see'] ?? [];
        $method->isIntersectionType = $array['isIntersectionType'] ?? false;


        foreach ($array['parameters'] as $parameter) {
            $method->addParameter(ParameterReflection::fromArray($project, $parameter));
        }

        return $method;
    }

    /**
     * Returns a method parameter.
     *
     * @param string $name Parameter name
     *
     * @return ParameterReflectionInterface|null
     */
    public function getParameter(string $name): ?ParameterReflectionInterface
    {
        if (ctype_digit($name)) {
            $tmp = array_values($this->parameters);

            return $tmp[$name] ?? null;
        }

        return $this->parameters[$name] ?? null;
    }

    /**
     * Returns all method parameters as an array.
     *
     * @return array
     */
    public function getParameters(): array
    {
        return $this->parameters;
    }

    /**
     * Returns the method source path.
     *
     * @return string
     */
    public function getSourcePath(): string
    {
        return $this->getClass()->getSourcePath($this->line);
    }

    /**
     * Check, if the method marks as `by reference`.
     *
     * @return bool
     */
    public function isByRef(): bool
    {
        return $this->byRef;
    }

    /**
     * Mark the method as `by reference` or not.
     *
     * @param bool $flag Flag
     *
     * @return void
     */
    public function setByRef(bool $flag): void
    {
        $this->byRef = $flag;
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
        if (0 === ($flag & ModifierInterface::VISIBILITY_MASK)) {
            $flag |= ModifierInterface::PUBLIC;
        }

        $this->traitSetModifier($flag);
    }

    /**
     * Set the method parameters as an array.
     *
     * @param array $parameters Array with method parameters
     *
     * @return void
     */
    public function setParameters(array $parameters): void
    {
        $this->parameters = $parameters;
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
            'see'                => $this->see,
            'modifiers'          => $this->modifier,
            'isByRef'            => $this->byRef,
            'isIntersectionType' => $this->isIntersectionType(),
            'exceptions'         => $this->exceptions,
            'errors'             => $this->errors,
            'parameters'         => array_map(
                static function ($parameter) {
                    return $parameter->toArray();
                },
                $this->parameters,
            ),
        ];
    }
}
