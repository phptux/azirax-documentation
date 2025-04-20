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
use Azirax\Documentation\Reflection\Interfaces\FunctionReflectionInterface;
use Azirax\Documentation\Reflection\Interfaces\ParameterReflectionInterface;
use Azirax\Documentation\Reflection\Traits\DocumentationTrait;
use Azirax\Documentation\Reflection\Traits\HintsTrait;
use Azirax\Documentation\Reflection\Traits\ModifierTrait;
use Azirax\Documentation\Reflection\Traits\TagsTrait;

use function array_values;
use function ctype_digit;
use function is_array;
use function trim;

/**
 * Reflection for `function`.
 *
 * @package      Azirax\Documentation\Reflection
 * @author       Rene Dziuba <php.tux@web.de>
 * @author       Fabien Potencier <fabien@symfony.com>
 * @copyright    Copyright (c) 2025 The Authors
 * @license      <https://opensource.org/license/mit> MIT License
 */
class FunctionReflection extends Reflection implements FunctionReflectionInterface
{
    use DocumentationTrait;
    use HintsTrait;
    use ModifierTrait;
    use TagsTrait;

    /**
     * Mark the function as `by reference`
     *
     * @var bool
     */
    protected bool $byRef = false;

    /**
     * Filename
     *
     * @var string|null
     */
    protected ?string $file = null;

    /**
     * Function loaded from cache?
     *
     * @var bool
     */
    protected bool $fromCache = false;

    /**
     * Function namespace name
     *
     * @var string|null
     */
    protected ?string $namespace = null;

    /**
     * Array with function parameters.
     *
     * @var ParameterReflectionInterface[]
     */
    protected array $parameters = [];

    /**
     * Relative file path
     *
     * @var string|null
     */
    protected ?string $relativeFilePath = null;

    /**
     * Magic method to string - returns the function name.
     *
     * @return string
     */
    public function __toString(): string
    {
        if ($this->namespace) {
            return $this->namespace . '\\' . $this->name;
        }

        return $this->name;
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
        $parameter->setFunction($this);
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
        $function                   = new self($array['name'], $array['line']);
        $function->shortDesc        = $array['shortDesc'];
        $function->longDesc         = $array['longDesc'];
        $function->hint             = $array['hint'];
        $function->hintDesc         = $array['hintDesc'];
        $function->tags             = $array['tags'];
        $function->byRef            = $array['isByRef'];
        $function->exceptions       = $array['exceptions'];
        $function->errors           = $array['errors'];
        $function->namespace        = $array['namespace'] ?? '';
        $function->file             = $array['file'] ?? '';
        $function->relativeFilePath = $array['relative_file'] ?? '';
        $function->fromCache        = true;

        if (isset($array['isIntersectionType'])) {
            $function->setIntersectionType($array['isIntersectionType']);
        }

        foreach ($array['parameters'] as $parameter) {
            $function->addParameter(ParameterReflection::fromArray($project, $parameter));
        }

        return $function;
    }

    /**
     * Returns all exceptions from the documentation.
     *
     * @return array
     */
    public function getExceptions(): array
    {
        $exceptions = [];

        foreach ($this->exceptions as $exception) {
            $exceptions[] = [
                $this->getProject()->getClass(is_array($exception) ? $exception[0] : $exception),
                '',
            ];
        }

        return $exceptions;
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
     * Returns all hints as an array.
     *
     * @return HintReflection[]
     */
    public function getHint(): array
    {
        if (empty($this->hint)) {
            return [];
        }

        $hints   = [];
        $project = $this->getProject();
        foreach ($this->hint as $hint) {
            $hints[] = new HintReflection(self::isPhpHint($hint[0]) ? $hint[0] : $project->getClass($hint[0]), $hint[1]);
        }

        return $hints;
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
     * Returns the relative file path.
     *
     * @return string|null
     */
    public function getRelativeFilePath(): ?string
    {
        return $this->relativeFilePath;
    }

    /**
     * Returns the function source path.
     *
     * @return string
     */
    public function getSourcePath(): string
    {
        if ($this->relativeFilePath === null) {
            return '';
        }

        return $this->project->getViewSourceUrl($this->relativeFilePath, $this->line);
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
     * Function data loaded from cache?
     *
     * @return bool
     */
    public function isFromCache(): bool
    {
        return $this->fromCache;
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
     * Set the namespace.
     *
     * @param string $namespace Namespace name
     *
     * @return void
     */
    public function setNamespace(string $namespace): void
    {
        $this->namespace = trim($namespace, '\\');
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
     * Returns the reflection data to array.
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'namespace'          => $this->namespace,
            'name'               => $this->name,
            'line'               => $this->line,
            'file'               => $this->file,
            'relative_file'      => $this->relativeFilePath,
            'shortDesc'          => $this->shortDesc,
            'longDesc'           => $this->longDesc,
            'hint'               => $this->hint,
            'hintDesc'           => $this->hintDesc,
            'tags'               => $this->tags,
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
