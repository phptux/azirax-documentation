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

namespace Azirax\Documentation\Version;

use Azirax\Documentation\Project;
use Countable;
use Iterator;
use ReflectionClass;

use function count;
use function func_get_args;
use function get_called_class;
use function is_array;

/**
 * Abstract version collection class.
 *
 * @package      Azirax\Documentation\Version
 * @author       Rene Dziuba <php.tux@web.de>
 * @author       Fabien Potencier <fabien@symfony.com>
 * @copyright    Copyright (c) 2025 The Authors
 * @license      <https://opensource.org/license/mit> MIT License
 */
abstract class VersionCollection implements Countable, Iterator
{
    /**
     * Position key from the iterator.
     *
     * @var int
     */
    protected int $position = 0;

    /**
     * Project object
     *
     * @var Project|null
     */
    protected ?Project $project = null;

    /**
     * Array with versions.
     *
     * @var Version[]
     */
    protected array $versions = [];

    /**
     * Constructor for VersionCollection
     *
     * @param array|string|Version $versions Version(s)
     */
    public function __construct(array|string|Version $versions)
    {
        $this->add($versions);
    }

    /**
     * Add the Version(s).
     *
     * @param array|string|Version $version  Version(s)
     * @param string|null          $longName Version long name
     *
     * @return $this
     */
    public function add(array|string|Version $version, ?string $longName = null): static
    {
        if (is_array($version)) {
            foreach ($version as $v) {
                $this->add($v);
            }
        } else {
            if (!$version instanceof Version) {
                $version = new Version($version, $longName);
            }

            $this->versions[] = $version;
        }

        return $this;
    }

    /**
     * Count versions of an object.
     *
     * @return int
     */
    public function count(): int
    {
        return count($this->versions);
    }

    /**
     * Create a new Version object.
     *
     * @return mixed
     */
    public static function create(): mixed
    {
        $r = new ReflectionClass(get_called_class());

        return $r->newInstanceArgs(func_get_args());
    }

    /**
     * Return the current version object.
     *
     * @return Version
     */
    public function current(): Version
    {
        return $this->versions[$this->position];
    }

    /**
     * Returns all register versions.
     *
     * @return Version[]
     */
    public function getVersions(): array
    {
        return $this->versions;
    }

    /**
     * Returns the current iterator key.
     *
     * @return int
     */
    public function key(): int
    {
        return $this->position;
    }

    /**
     * Move forward to the next version.
     *
     * @return void
     */
    public function next(): void
    {
        ++$this->position;
    }

    /**
     * Rewind the Iterator to the first version.
     *
     * @return void
     */
    public function rewind(): void
    {
        $this->position = 0;
    }

    /**
     * Set the project object.
     *
     * @param Project $project Project object
     *
     * @return void
     */
    public function setProject(Project $project): void
    {
        $this->project = $project;
    }

    /**
     * Checks if current position is valid.
     *
     * @return bool
     */
    public function valid(): bool
    {
        if ($this->position < count($this->versions)) {
            $this->switchVersion($this->current());

            return true;
        }

        return false;
    }

    /**
     * Switch to version.
     *
     * @param Version $version Version object
     *
     * @return void
     */
    abstract protected function switchVersion(Version $version): void;

}
