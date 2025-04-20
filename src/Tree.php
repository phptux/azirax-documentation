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

namespace Azirax\Documentation;

use Azirax\Documentation\Reflection\Interfaces\ClassReflectionInterface;
use Wdes\phpI18nL10n\Launcher;

use function array_slice;
use function count;
use function explode;
use function implode;
use function strpos;
use function substr;

/**
 * Tree class.
 *
 * @package      Azirax\Documentation
 * @author       Rene Dziuba <php.tux@web.de>
 * @author       Fabien Potencier <fabien@symfony.com>
 * @copyright    Copyright (c) 2025 The Authors
 * @license      <https://opensource.org/license/mit> MIT License
 */
class Tree
{
    /**
     * Returns the global namespace page name.
     *
     * @return string
     */
    public static function getGlobalNamespacePageName(): string
    {
        return Launcher::gettext('[Global_Namespace]');
    }

    /**
     * Returns the global namespace name.
     *
     * @return string
     */
    public static function getGlobalNamespaceName(): string
    {
        return Launcher::gettext('[Global Namespace]');
    }

    /**
     * Returns the tree from the project.
     *
     * @param Project $project  Project object
     *
     * @return TreeNode
     */
    public function getTree(Project $project): TreeNode
    {
        $namespaces = [];
        $ns         = $project->getNamespaces();
        foreach ($ns as $namespace) {
            if (false !== $pos = strpos($namespace, '\\')) {
                $namespaces[substr($namespace, 0, $pos)][] = $namespace;
            } else {
                $namespaces[$namespace][] = $namespace;
            }
        }

        return new TreeNode(0, '', '', $this->generateClassTreeLevel($project, 1, $namespaces, []));
    }

    /**
     * Generate the class level tree.
     *
     * @param Project                    $project   Project object
     * @param int                        $level Level
     * @param array                      $namespaces Namespaces
     * @param ClassReflectionInterface[] $classes    Array with classes
     *
     * @return TreeNode[]
     */
    protected function generateClassTreeLevel(Project $project, int $level, array $namespaces, array $classes): array
    {
        ++$level;

        $treeNodes         = [];
        $currentHumanLevel = $level - 1;
        foreach ($namespaces as $namespace => $subNamespaces) {
            // classes
            $cl = $project->getNamespaceAllClasses($namespace);

            // sub namespaces
            $ns = [];
            foreach ($subNamespaces as $subNamespace) {
                $parts = explode('\\', $subNamespace);
                if (!isset($parts[$currentHumanLevel])) {
                    continue;
                }

                $ns[implode('\\', array_slice($parts, 0, $level))][] = $subNamespace;
            }

            $parts = explode('\\', $namespace);
            $url   = Tree::getGlobalNamespacePageName();

            $namespaceParentPart = $parts[count($parts) - 1] ?? null;

            if ($namespaceParentPart && $project->hasNamespace($namespace) && (count($subNamespaces) || count($cl))) {
                $url = $namespace;
            }

            $short = $namespaceParentPart ?: self::getGlobalNamespaceName();

            $treeNodes[] = new TreeNode($currentHumanLevel, $short, $url, $this->generateClassTreeLevel($project, $level, $ns, $cl));
        }

        foreach ($classes as $class) {
            $treeNodes[] = new TreeNode($currentHumanLevel, $class->getShortName(), $class->getName(), null);
        }

        return $treeNodes;
    }
}
