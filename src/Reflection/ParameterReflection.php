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
use Azirax\Documentation\Reflection\Interfaces\FunctionReflectionInterface;
use Azirax\Documentation\Reflection\Interfaces\MethodReflectionInterface;
use Azirax\Documentation\Reflection\Interfaces\ParameterReflectionInterface;
use Azirax\Documentation\Reflection\Traits\ClassTrait;
use Azirax\Documentation\Reflection\Traits\DocumentationTrait;
use Azirax\Documentation\Reflection\Traits\HintsTrait;
use Azirax\Documentation\Reflection\Traits\ModifierTrait;
use Azirax\Documentation\Reflection\Traits\TagsTrait;

use function count;
use function is_array;
use function strtolower;

/**
 * Reflection class for parameters.
 *
 * @package      Azirax\Documentation\Reflection
 * @author       Rene Dziuba <php.tux@web.de>
 * @author       Fabien Potencier <fabien@symfony.com>
 * @copyright    Copyright (c) 2025 The Authors
 * @license      <https://opensource.org/license/mit> MIT License
 */
class ParameterReflection extends Reflection implements ParameterReflectionInterface
{
    use ClassTrait;
    use DocumentationTrait;
    use HintsTrait;
    use ModifierTrait;
    use TagsTrait;

    /**
     * Mark the parameter as `by reference`
     *
     * @var bool
     */
    protected bool $byRef = false;

    /**
     * Parameter default value.
     *
     * @var mixed
     */
    protected mixed $default = null;

    /**
     * FunctionReflection object
     *
     * @var FunctionReflectionInterface|null
     */
    protected ?FunctionReflectionInterface $function = null;

    /**
     * MethodReflection object
     *
     * @var MethodReflectionInterface|null
     */
    protected ?MethodReflectionInterface $method = null;

    /**
     * Mark the parameter as `variadic`
     *
     * @var bool
     */
    protected bool $variadic = false;

    /**
     * Magic method to string - returns the class name.
     *
     * @return string
     */
    public function __toString(): string
    {
        if ($this->method) {
            return $this->method . '#' . $this->name;
        }

        return '#' . $this->name;
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
        $parameter                     = new self($array['name'], $array['line']);
        $parameter->shortDesc          = $array['shortDesc'];
        $parameter->longDesc           = $array['longDesc'];
        $parameter->hint               = $array['hint'];
        $parameter->tags               = $array['tags'];
        $parameter->default            = $array['default'];
        $parameter->variadic           = $array['variadic'];
        $parameter->byRef              = $array['isByRef'];
        $parameter->isReadOnly         = $array['isReadOnly'];
        $parameter->isIntersectionType = $array['isIntersectionType'];

        return $parameter;
    }

    /**
     * Returns the class reflection object.
     *
     * @return ClassReflection|null
     */
    public function getClass(): ?ClassReflection
    {
        return $this->getMethod()->getClass();
    }

    /**
     * Returns the default value.
     *
     * @return mixed
     */
    public function getDefault(): mixed
    {
        return $this->default;
    }

    /**
     * Returns the FunctionReflection object.
     *
     * @return FunctionReflectionInterface|null
     */
    public function getFunction(): ?FunctionReflectionInterface
    {
        return $this->function;
    }

    /**
     * Returns the hints.
     *
     * @return HintReflection[]
     */
    public function getHint(): array
    {
        if (count($this->hint) < 1) {
            return [];
        }

        $hints = [];
        if ($this->function !== null) {
            /** @var FunctionReflection $function */
            $function = $this->getFunction();
            $project  = $function->getProject();
        } else {
            $class   = $this->getClass();
            $project = $class->getProject();
        }
        foreach ($this->hint as $hint) {
            $hints[] = new HintReflection(self::isPhpHint($hint[0]) ? strtolower($hint[0]) : $project->getClass($hint[0]), $hint[1]);
        }

        return $hints;
    }

    /**
     * Returns the MethodReflection object.
     *
     * @return MethodReflectionInterface|null
     */
    public function getMethod(): ?MethodReflectionInterface
    {
        return $this->method;
    }

    /**
     * Check, if the parameter marks as `variadic`.
     *
     * @return bool
     */
    public function getVariadic(): bool
    {
        return $this->variadic;
    }

    /**
     * Check, if the parameter marks as `by reference`.
     *
     * @return bool
     */
    public function isByRef(): bool
    {
        return $this->byRef;
    }

    /**
     * Mark the parameter as `by reference` or not.
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
     * Set default value.
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
     * Set the FunctionReflection object.
     *
     * @param FunctionReflectionInterface $function FunctionReflection object
     *
     * @return void
     */
    public function setFunction(FunctionReflectionInterface $function): void
    {
        $this->function = $function;
    }

    /**
     * Set the MethodReflection object.
     *
     * @param MethodReflectionInterface $method MethodReflection object
     *
     * @return void
     */
    public function setMethod(MethodReflectionInterface $method): void
    {
        $this->method = $method;
    }

    /**
     * Mark the parameter as `variadic` or not.
     *
     * @param bool $flag Flag
     *
     * @return void
     */
    public function setVariadic(bool $flag): void
    {
        $this->variadic = $flag;
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
            'tags'               => $this->tags,
            'default'            => $this->default,
            'variadic'           => $this->variadic,
            'isByRef'            => $this->byRef,
            'isReadOnly'         => $this->isReadOnly(),
            'isIntersectionType' => $this->isIntersectionType(),
        ];
    }

}
