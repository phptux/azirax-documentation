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

namespace Azirax\Documentation\Store;

use Azirax\Documentation\Project;
use Azirax\Documentation\Reflection\ClassReflection;
use Azirax\Documentation\Reflection\FunctionReflection;
use Azirax\Documentation\Reflection\Interfaces\ClassReflectionInterface;
use InvalidArgumentException;

use function array_merge;
use function sprintf;

/**
 * Stores the project data in-memory.
 *
 * Mainly useful for unit tests.
 *
 * @package      Azirax\Documentation\Store
 * @author       Rene Dziuba <php.tux@web.de>
 * @author       Fabien Potencier <fabien@symfony.com>
 * @copyright    Copyright (c) 2025 The Authors
 * @license      <https://opensource.org/license/mit> MIT License
 */
class ArrayStore implements StoreInterface
{
    /**
     * Array with class data.
     *
     * @var array
     */
    private array $classes = [];

    /**
     * Array with function data.
     *
     * @var array
     */
    private array $functions = [];

    /**
     * Remove all project data from the store.
     *
     * @param Project $project Project object
     *
     * @return void
     */
    public function flushProject(Project $project): void
    {
        $this->classes   = [];
        $this->functions = [];
    }

    /**
     * Read the class data from the store.
     *
     * @param Project $project Project object
     * @param string  $name    Class name
     *
     * @return ClassReflection
     */
    public function readClass(Project $project, string $name): ClassReflection
    {
        if (!isset($this->classes[$name])) {
            throw new InvalidArgumentException(sprintf('Class "%s" does not exist.', $name));
        }

        return $this->classes[$name];
    }

    /**
     * Read the function data from the store.
     *
     * @param Project $project Project object
     * @param string  $name    Function name
     *
     * @return FunctionReflection
     */
    public function readFunction(Project $project, string $name): FunctionReflection
    {
        if (!isset($this->functions[$name])) {
            throw new InvalidArgumentException(sprintf('Function "%s" does not exist.', $name));
        }

        return $this->functions[$name];
    }

    /**
     * Read the project from the store.
     *
     * @param Project $project Project object
     *
     * @return array
     */
    public function readProject(Project $project): array
    {
        return array_merge($this->classes, $this->functions);
    }

    /**
     * Remove the class data from the store.
     *
     * @param Project $project Project object
     * @param string  $name    Class name
     *
     * @return void
     */
    public function removeClass(Project $project, string $name): void
    {
        if (!isset($this->classes[$name])) {
            throw new InvalidArgumentException(sprintf('Class "%s" does not exist.', $name));
        }

        unset($this->classes[$name]);
    }

    /**
     * Remove the function data from the store.
     *
     * @param Project $project Project object
     * @param string  $name    Function name
     *
     * @return void
     */
    public function removeFunction(Project $project, string $name): void
    {
        if (!isset($this->functions[$name])) {
            throw new InvalidArgumentException(sprintf('Function "%s" does not exist.', $name));
        }

        unset($this->functions[$name]);
    }

    /**
     * Set classes as a class reflection array.
     *
     * @param ClassReflectionInterface[] $classes Classes
     *
     * @return void
     */
    public function setClasses(array $classes): void
    {
        foreach ($classes as $class) {
            $this->classes[$class->getName()] = $class;
        }
    }

    /**
     * Write the class data in the store.
     *
     * @param Project         $project Project object
     * @param ClassReflection $class   Class object
     *
     * @return void
     */
    public function writeClass(Project $project, ClassReflection $class): void
    {
        $this->classes[$class->getName()] = $class;
    }

    /**
     * Write the function data in the store.
     *
     * @param Project            $project  Project object
     * @param FunctionReflection $function Function object
     *
     * @return void
     */
    public function writeFunction(Project $project, FunctionReflection $function): void
    {
        $this->functions[$function->getName()] = $function;
    }

}
