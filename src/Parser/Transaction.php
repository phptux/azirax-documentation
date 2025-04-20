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
use Azirax\Documentation\Reflection\Interfaces\ClassReflectionInterface;

use function array_key_exists;
use function array_keys;
use function array_merge;

/**
 * Parser transaction class.
 *
 * @package      Azirax\Documentation\Parser
 * @author       Rene Dziuba <php.tux@web.de>
 * @author       Fabien Potencier <fabien@symfony.com>
 * @copyright    Copyright (c) 2025 The Authors
 * @license      <https://opensource.org/license/mit> MIT License
 */
class Transaction
{
    /**
     * Classes names with hash
     *
     * @var array
     */
    protected array $classes = [];

    /**
     * Class hashes and names
     *
     * @var array
     */
    protected array $hashes = [];

    /**
     * Array with modified classes
     *
     * @var array
     */
    protected array $modified = [];

    /**
     * Array with visited class hashes
     *
     * @var array
     */
    protected array $visited = [];

    /**
     * Constructor for Transaction
     *
     * @param Project $project Project object
     */
    public function __construct(Project $project)
    {
        foreach ($project->getProjectClasses() as $class) {
            $this->addClass($class);
        }

        $this->visited  = [];
        $this->modified = [];
    }

    /**
     * Add a class to the transaction.
     *
     * @param ClassReflectionInterface $class Class reflection object
     *
     * @return void
     */
    public function addClass(ClassReflectionInterface $class): void
    {
        $name = $class->getName();
        $hash = $class->getHash();

        if (isset($this->classes[$name])) {
            unset($this->hashes[$this->classes[$name]][$name]);
            if (!$this->hashes[$this->classes[$name]]) {
                unset($this->hashes[$this->classes[$name]]);
            }
        }

        $this->hashes[$hash][$name] = true;
        $this->classes[$name]       = $hash;
        $this->modified[]           = $name;
        $this->visited[$hash]       = true;
    }

    /**
     * Returns all modified classes.
     *
     * @return array
     */
    public function getModifiedClasses(): array
    {
        return $this->modified;
    }

    /**
     * Returns all removed classes.
     *
     * @return array
     */
    public function getRemovedClasses(): array
    {
        $classes = [];
        foreach ($this->hashes as $hash => $c) {
            if (!isset($this->visited[$hash])) {
                $classes = array_merge($classes, $c);
            }
        }

        return array_keys($classes);
    }

    /**
     * Check, if the class hash exists.
     *
     * @param string $hash Class hash string
     *
     * @return bool
     */
    public function hasHash(string $hash): bool
    {
        if (!array_key_exists($hash, $this->hashes)) {
            return false;
        }

        $this->visited[$hash] = true;

        return true;
    }
}
