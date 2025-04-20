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

namespace Azirax\Documentation\Reflection\Interfaces;

use Azirax\Documentation\Reflection\HintReflection;

/**
 * Interface for hints.
 *
 * @package      Azirax\Documentation\Reflection\Interfaces
 * @author       Rene Dziuba <php.tux@web.de>
 * @author       Fabien Potencier <fabien@symfony.com>
 * @copyright    Copyright (c) 2025 The Authors
 * @license      <https://opensource.org/license/mit> MIT License
 */
interface HintsInterface
{
    /**
     * Returns all hints as an array.
     *
     * @return HintReflection[]
     */
    public function getHint(): array;

    /**
     * Returns the hint as string.
     *
     * @return string
     */
    public function getHintAsString(): string;

    /**
     * Returns the hint description.
     *
     * @return string|null
     */
    public function getHintDesc(): ?string;

    /**
     * Returns all hints as raw.
     *
     * @return array|ClassReflectionInterface[]
     */
    public function getRawHint(): array;

    /**
     * Check, if we have hint(s).
     *
     * @return bool
     */
    public function hasHint(): bool;

    /**
     * Add or set hint(s).
     *
     * @param array|ClassReflectionInterface|string $hint Hint(s)
     */
    public function setHint(array|ClassReflectionInterface|string $hint): void;

    /**
     * Set the hint description.
     *
     * @param string $desc Hint description
     */
    public function setHintDesc(string $desc): void;

    /**
     * Mark the hint as intersection Type or not.
     *
     * @param bool $flag
     *
     * @return void
     */
    public function setIntersectionType(bool $flag): void;

    /**
     * Check, if they hint an intersection Type.
     *
     * @return bool
     */
    public function isIntersectionType(): bool;
}
