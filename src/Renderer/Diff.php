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

use function array_diff;
use function array_keys;
use function count;
use function file_exists;
use function file_get_contents;
use function file_put_contents;
use function is_string;
use function serialize;
use function unserialize;

/**
 * Diff class.
 *
 * @package      Azirax\Documentation\Renderer
 * @author       Rene Dziuba <php.tux@web.de>
 * @author       Fabien Potencier <fabien@symfony.com>
 * @copyright    Copyright (c) 2025 The Authors
 * @license      <https://opensource.org/license/mit> MIT License
 */
class Diff
{
    /**
     * Flag for rendered
     *
     * @var bool
     */
    protected bool $alreadyRendered = false;

    /**
     * Data from the current project
     *
     * @var Index
     */
    protected Index $current;

    /**
     * Array with current namespaces
     *
     * @var array
     */
    protected array $currentNamespaces;

    /**
     * Cache filename
     *
     * @var string|null
     */
    protected ?string $filename = null;

    /**
     * Previous project Index object
     *
     * @var Index
     */
    private Index $previous;

    /**
     * Array with previous namespaces
     *
     * @var array
     */
    protected array $previousNamespaces;

    /**
     * Project object
     *
     * @var Project
     */
    protected Project $project;

    /**
     * Array with versions.
     *
     * @var array
     */
    protected array $versions = [];

    /**
     * Constructor for Diff
     *
     * @param Project     $project  Project object
     * @param string|null $filename Cache filename
     */
    public function __construct(Project $project, ?string $filename = null)
    {
        $this->project  = $project;
        $this->current  = new Index($project);
        $this->filename = $filename;

        if (is_string($filename) && file_exists($filename)) {
            $this->alreadyRendered = true;
            $previous              = $this->readSerializedFile($filename);
            if (null === $previous) {
                $this->alreadyRendered = false;
                $this->previous        = new Index();
            } else {
                $this->previous = $previous;
            }
        } else {
            $this->alreadyRendered = false;
            $this->previous        = new Index();
        }

        $this->previousNamespaces = $this->previous->getNamespaces();
        $this->currentNamespaces  = $this->current->getNamespaces();
    }

    /**
     * Check, if all versions modified.
     *
     * @return bool
     */
    public function areVersionsModified(): bool
    {
        $versions = [];
        foreach ($this->project->getVersions() as $version) {
            $versions[] = (string)$version;
        }

        return $versions != $this->previous->getVersions();
    }

    /**
     * Returns all modified classes.
     *
     * @return array
     */
    public function getModifiedClasses(): array
    {
        $classes = [];

        foreach ($this->current->getClasses() as $class => $hash) {
            if ($hash !== $this->previous->getHash($class)) {
                $classes[] = $this->project->getClass($class);
            }
        }

        return $classes;
    }

    /**
     * Returns all modified namespaces.
     *
     * @return array
     */
    public function getModifiedNamespaces(): array
    {
        return array_diff($this->currentNamespaces, $this->previousNamespaces);
    }

    /**
     * Returns all removed classes.
     *
     * @return array
     */
    public function getRemovedClasses(): array
    {
        return array_diff(array_keys($this->previous->getClasses()), array_keys($this->current->getClasses()));
    }

    /**
     * Returns all removed namespaces.
     *
     * @return array
     */
    public function getRemovedNamespaces(): array
    {
        return array_diff($this->previousNamespaces, $this->currentNamespaces);
    }

    /**
     * Check, if is already rendered.
     *
     * @return bool
     */
    public function isAlreadyRendered(): bool
    {
        return $this->alreadyRendered;
    }

    /**
     * Project is empty?
     *
     * @return bool
     */
    public function isEmpty(): bool
    {
        return !$this->areVersionsModified() && (0 == count($this->getModifiedClasses()) + count($this->getRemovedClasses()));
    }

    /**
     * Save the current project.
     *
     * @return void
     */
    public function save(): void
    {
        if (is_string($this->filename)) {
            file_put_contents($this->filename, serialize($this->current));
        }
    }

    /**
     * Read the serialized cache file.
     *
     * @param string $filename  Cache filename
     *
     * @return Index|null
     */
    protected function readSerializedFile(string $filename): ?Index
    {
        $contents = file_get_contents($filename);

        if ($contents === false) {
            return null;
        }
        $contents = @unserialize($contents);

        return $contents === false ? null : $contents;
    }
}
