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

namespace Azirax\Documentation\Renderer;

use Azirax\Documentation\Project;

/**
 * Index
 *
 * @package      Azirax\Documentation\Renderer
 * @author       Rene Dziuba <php.tux@web.de>
 * @author       Fabien Potencier <fabien@symfony.com>
 * @copyright    Copyright (c) 2025 The Authors
 * @license      <https://opensource.org/license/mit> MIT License
 */
class Index
{
    /**
     * Array with classes
     *
     * @var array
     */
    protected array $classes = [];

    /**
     * Array with namespaces
     *
     * @var array
     */
    protected array $namespaces = [];

    /**
     * Array with versions
     *
     * @var array
     */
    protected array $versions = [];

    /**
     * Constructor for Index
     *
     * @param Project|null $project Project object
     */
    public function __construct(Project $project = null)
    {
        if (null !== $project) {
            foreach ($project->getProjectClasses() as $class) {
                $this->classes[$class->getName()] = $class->getHash();
            }
            foreach ($project->getProjectInterfaces() as $class) {
                $this->classes[$class->getName()] = $class->getHash();
            }
            foreach ($project->getProjectTraits() as $class) {
                $this->classes[$class->getName()] = $class->getHash();
            }
            foreach ($project->getProjectEnums() as $class) {
                $this->classes[$class->getName()] = $class->getHash();
            }
        }

        if (null !== $project) {
            foreach ($project->getVersions() as $version) {
                $this->versions[] = (string)$version;
            }
        }

        if (null !== $project) {
            $this->namespaces = $project->getNamespaces();
        }
    }

    /**
     * Returns the class data for serialize the class.
     *
     * @return array
     */
    public function __serialize(): array
    {
        return [
            'c' => $this->classes,
            'n' => $this->namespaces,
            'v' => $this->versions,
        ];
    }

    /**
     * Unserialize the class.
     *
     * @param array $data Array with class data
     *
     * @return void
     */
    public function __unserialize(array $data): void
    {
        $this->classes    = $data['c'];
        $this->namespaces = $data['n'];
        $this->versions   = $data['v'];
    }

    /**
     * Returns all classes.
     *
     * @return array
     */
    public function getClasses(): array
    {
        return $this->classes;
    }

    /**
     * Returns the hash string from the class.
     *
     * @param string $class Class name
     *
     * @return string|null
     */
    public function getHash(string $class): ?string
    {
        return $this->classes[$class] ?? null;
    }

    /**
     * Returns all namespaces.
     *
     * @return array
     */
    public function getNamespaces(): array
    {
        return $this->namespaces;
    }

    /**
     * Returns all versions.
     *
     * @return array
     */
    public function getVersions(): array
    {
        return $this->versions;
    }
}
