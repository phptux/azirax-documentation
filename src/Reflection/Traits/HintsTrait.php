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

namespace Azirax\Documentation\Reflection\Traits;

use Azirax\Documentation\Reflection\HintReflection;
use Azirax\Documentation\Reflection\Interfaces\ClassReflectionInterface;

use function implode;
use function is_array;
use function strtolower;

/**
 * Trait for hints.
 *
 * @package      Azirax\Documentation\Reflection\Traits
 * @author       Rene Dziuba <php.tux@web.de>
 * @author       Fabien Potencier <fabien@symfony.com>
 * @copyright    Copyright (c) 2025 The Authors
 * @license      <https://opensource.org/license/mit> MIT License
 */
trait HintsTrait
{
    /**
     * Hint(s)
     *
     * @var array
     */
    protected array $hint = [];

    /**
     * Hint description
     *
     * @var string|null
     */
    protected ?string $hintDesc = null;

    /**
     * Flag for intersection Type
     *
     * @var bool
     */
    protected bool $isIntersectionType = false;

    /**
     * Returns all hints as an array.
     *
     * @return HintReflection[]
     */
    public function getHint(): array
    {
        if (empty($this->hint)) {
            return [];
        }

        $hints   = [];
        $class   = $this->getClass();
        $project = $class->getProject();
        foreach ($this->hint as $hint) {
            $hints[] = new HintReflection(self::isPhpHint($hint[0]) ? strtolower($hint[0]) : $project->getClass($hint[0]), $hint[1]);
        }

        return $hints;
    }

    /**
     * Returns the hint as string.
     *
     * @return string
     */
    public function getHintAsString(): string
    {
        $str = [];
        foreach ($this->getHint() as $hint) {
            $str[] = ($hint->isClass() ? $hint->getClass()->getShortName() : $hint->getName()) . ($hint->isArray() ? '[]' : '');
        }

        return implode('|', $str);
    }

    /**
     * Returns the hint description.
     *
     * @return string|null
     */
    public function getHintDesc(): ?string
    {
        return $this->hintDesc;
    }

    /**
     * Returns all hints as raw.
     *
     * @return array|ClassReflectionInterface[]
     */
    public function getRawHint(): array
    {
        return $this->hint;
    }

    /**
     * Check, if we have hint(s).
     *
     * @return bool
     */
    public function hasHint(): bool
    {
        return !empty($this->hint);
    }

    /**
     * Check, if they hint an intersection Type.
     *
     * @return bool
     */
    public function isIntersectionType(): bool
    {
        return $this->isIntersectionType;
    }

    /**
     * Add or set hint(s).
     *
     * @param array|ClassReflectionInterface|string $hint Hint(s)
     */
    public function setHint(array|ClassReflectionInterface|string $hint): void
    {
        if (is_array($hint)) {
            $this->hint = $hint;
        } else {
            $this->hint[] = $hint;
        }
    }

    /**
     * Set the hint description.
     *
     * @param string $desc Hint description
     */
    public function setHintDesc(string $desc): void
    {
        $this->hintDesc = $desc;
    }

    /**
     * Mark the hint as intersection Type or not.
     *
     * @param bool $flag
     *
     * @return void
     */
    public function setIntersectionType(bool $flag): void
    {
        $this->isIntersectionType = $flag;
    }
}
