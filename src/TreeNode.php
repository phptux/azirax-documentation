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

use JsonSerializable;

use function str_replace;

/**
 * Tree node.
 *
 * @package      Azirax\Documentation
 * @author       Rene Dziuba <php.tux@web.de>
 * @author       Fabien Potencier <fabien@symfony.com>
 * @copyright    Copyright (c) 2025 The Authors
 * @license      <https://opensource.org/license/mit> MIT License
 */
class TreeNode implements JsonSerializable
{
    /**
     * Tree node children
     *
     * @var TreeNode[]|null
     */
    protected ?array $children = null;

    /**
     * Level
     *
     * @var int
     */
    protected int $level;

    /**
     * Name
     *
     * @var string
     */
    protected string $name;

    /**
     * Path
     *
     * @var string
     */
    protected string $path;

    /**
     * Constructor for TreeNode
     *
     * @param int             $level    Level
     * @param string          $name     Name
     * @param string          $path     Path
     * @param TreeNode[]|null $children Children
     */
    public function __construct(
        int $level,
        string $name,
        string $path,
        ?array $children,
    ) {
        $this->level    = $level;
        $this->name     = $name;
        $this->path     = $path;
        $this->children = $children;
    }

    /**
     * Returns the tree node children.
     *
     * @return TreeNode[]|null
     */
    public function getChildren(): ?array
    {
        return $this->children;
    }

    /**
     * Returns the level.
     *
     * @return int
     */
    public function getLevel(): int
    {
        return $this->level;
    }

    /**
     * Returns name.
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Returns the path.
     *
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * Check, if tree node has children.
     *
     * @return bool
     */
    public function hasChildren(): bool
    {
        return $this->children !== null;
    }

    /**
     * Returns the tree node in JSON format.
     *
     * @return array
     */
    public function jsonSerialize(): array
    {
        if ($this->children === null || $this->children === []) {
            return [
                'l' => $this->level,
                'n' => $this->name,
                'p' => str_replace('\\', '/', $this->path),
            ];
        }
        return [
            'l' => $this->level,
            'n' => $this->name,
            'p' => str_replace('\\', '/', $this->path),
            'c' => $this->children,
        ];
    }
}
