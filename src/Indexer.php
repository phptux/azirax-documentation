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

use function preg_replace;
use function strtolower;

/**
 * Indexer
 *
 * @package      Azirax\Documentation
 * @author       Rene Dziuba <php.tux@web.de>
 * @author       Fabien Potencier <fabien@symfony.com>
 * @copyright    Copyright (c) 2025 The Authors
 * @license      <https://opensource.org/license/mit> MIT License
 */
class Indexer
{
    /**
     * Index: class
     */
    private const int TYPE_CLASS = 1;

    /**
     * Index: function
     */
    private const int TYPE_FUNCTION = 4;

    /**
     * Index: method
     */
    private const int TYPE_METHOD = 2;

    /**
     * Index: namespace
     */
    private const int TYPE_NAMESPACE = 3;

    /**
     * Returns the search indexes.
     *
     * @param Project $project Project object
     *
     * @return array
     */
    public function getIndex(Project $project): array
    {
        $index = [
            'searchIndex' => [],
            'info'        => [],
        ];

        foreach ($project->getNamespaces() as $namespace) {
            $index['searchIndex'][] = $this->getSearchString($namespace);
            $index['info'][]        = [self::TYPE_NAMESPACE, $namespace];
        }

        foreach ($project->getProjectClasses() as $class) {
            $index['searchIndex'][] = $this->getSearchString((string)$class);
            $index['info'][]        = [self::TYPE_CLASS, $class];
        }

        foreach ($project->getProjectClasses() as $class) {
            foreach ($class->getMethods() as $method) {
                $index['searchIndex'][] = $this->getSearchString((string)$method);
                $index['info'][]        = [self::TYPE_METHOD, $method];
            }
        }

        foreach ($project->getProjectFunctions() as $function) {
            $index['searchIndex'][] = $this->getSearchString((string)$function);
            $index['info'][]        = [self::TYPE_FUNCTION, $function];
        }

        return $index;
    }

    /**
     * Returns the search string.
     *
     * @param string $string String
     *
     * @return string
     */
    protected function getSearchString(string $string): string
    {
        return strtolower(preg_replace('/\s+/', '', $string));
    }
}
