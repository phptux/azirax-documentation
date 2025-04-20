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

namespace Azirax\Documentation\Parser;

use Azirax\Documentation\Project;
use SplObjectStorage;

/**
 * Project traverser class.
 *
 * @package      Azirax\Documentation\Parser
 * @author       Rene Dziuba <php.tux@web.de>
 * @author       Fabien Potencier <fabien@symfony.com>
 * @copyright    Copyright (c) 2025 The Authors
 * @license      <https://opensource.org/license/mit> MIT License
 */
class ProjectTraverser
{
    /**
     * The class visitors
     *
     * @var ClassVisitorInterface[]
     */
    protected array $classVisitors = [];

    /**
     * The function visitors
     *
     * @var FunctionVisitorInterface[]
     */
    protected array $functionVisitors = [];

    /**
     * Constructor for ProjectTraverser
     *
     * @param ClassVisitorInterface[]|FunctionVisitorInterface[] $visitors  Visetor objects
     */
    public function __construct(array $visitors = [])
    {
        foreach ($visitors as $visitor) {
            if ($visitor instanceof ClassVisitorInterface) {
                $this->addClassVisitor($visitor);
            }
            if ($visitor instanceof FunctionVisitorInterface) {
                $this->addFunctionVisitor($visitor);
            }
        }
    }

    /**
     * Add a class visitor.
     *
     * @param ClassVisitorInterface $visitor    Class visitor object
     *
     * @return void
     */
    public function addClassVisitor(ClassVisitorInterface $visitor): void
    {
        $this->classVisitors[] = $visitor;
    }

    /**
     * Add a function visitor.
     *
     * @param FunctionVisitorInterface $visitor    Function visitor object
     *
     * @return void
     */
    public function addFunctionVisitor(FunctionVisitorInterface $visitor): void
    {
        $this->functionVisitors[] = $visitor;
    }

    /**
     * Traverse the project.
     *
     * @param Project $project  Project object
     *
     * @return SplObjectStorage
     */
    public function traverse(Project $project): SplObjectStorage
    {
        // parent classes/interfaces are visited before their "children"
        $classes  = $project->getProjectClasses();
        $modified = new SplObjectStorage();
        while ($class = array_shift($classes)) {
            // re-push the class at the end if parent class/interfaces have not been visited yet
            if (($parent = $class->getParent()) && isset($classes[$parent->getName()])) {
                $classes[$class->getName()] = $class;

                continue;
            }

            $interfaces = $class->getInterfaces();
            foreach ($interfaces as $interface) {
                if (isset($classes[$interface->getName()])) {
                    $classes[$class->getName()] = $class;

                    continue 2;
                }
            }

            // only visits classes not coming from the cache
            // and for which parent/interfaces also come from the cache
            $visit = !$class->isFromCache() || ($parent && !$parent->isFromCache());
            foreach ($interfaces as $interface) {
                if (!$interface->isFromCache()) {
                    $visit = true;

                    break;
                }
            }

            if (!$visit) {
                continue;
            }

            $isModified = false;
            foreach ($this->classVisitors as $visitor) {
                $isModified = $visitor->visit($class) || $isModified;
            }

            if ($isModified) {
                $modified->attach($class);
            }
        }

        $functions = $project->getProjectFunctions();
        foreach ($functions as $function) {
            if ($function->isFromCache()) {
                continue;
            }

            $isModifiedFunction = false;
            foreach ($this->functionVisitors as $visitor) {
                $isModifiedFunction = $visitor->visit($function) || $isModifiedFunction;
            }

            if ($isModifiedFunction) {
                $modified->attach($function);
            }
        }

        return $modified;
    }
}
