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

/**
 * Interface for all store classes.
 *
 * @package      Azirax\Documentation\Store
 * @author       Rene Dziuba <php.tux@web.de>
 * @author       Fabien Potencier <fabien@symfony.com>
 * @copyright    Copyright (c) 2025 The Authors
 * @license      <https://opensource.org/license/mit> MIT License
 */
interface StoreInterface
{
    /**
     * Remove all project data from the store.
     *
     * @param Project $project Project object
     *
     * @return void
     */
    public function flushProject(Project $project): void;

    /**
     * Read the class data from the store.
     *
     * @param Project $project Project object
     * @param string  $name    Class name
     *
     * @return ClassReflection
     */
    public function readClass(Project $project, string $name): ClassReflection;

    /**
     * Read the function data from the store.
     *
     * @param Project $project Project object
     * @param string  $name    Function name
     *
     * @return FunctionReflection
     */
    public function readFunction(Project $project, string $name): FunctionReflection;

    /**
     * Read the project from the store.
     *
     * @param Project $project Project object
     *
     * @return array
     */
    public function readProject(Project $project): array;

    /**
     * Remove the class data from the store.
     *
     * @param Project $project Project object
     * @param string  $name    Class name
     *
     * @return void
     */
    public function removeClass(Project $project, string $name): void;

    /**
     * Remove the function data from the store.
     *
     * @param Project $project Project object
     * @param string  $name    Function name
     *
     * @return void
     */
    public function removeFunction(Project $project, string $name): void;

    /**
     * Write the class data in the store.
     *
     * @param Project         $project Project object
     * @param ClassReflection $class   Class object
     *
     * @return void
     */
    public function writeClass(Project $project, ClassReflection $class): void;

    /**
     * Write the function data in the store.
     *
     * @param Project            $project  Project object
     * @param FunctionReflection $function Function object
     *
     * @return void
     */
    public function writeFunction(Project $project, FunctionReflection $function): void;
}
