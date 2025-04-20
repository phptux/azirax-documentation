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

/**
 * Interface for modifiers.
 *
 * @package      Azirax\Documentation\Reflection\Interfaces
 * @author       Rene Dziuba <php.tux@web.de>
 * @author       Fabien Potencier <fabien@symfony.com>
 * @copyright    Copyright (c) 2025 The Authors
 * @license      <https://opensource.org/license/mit> MIT License
 */
interface ModifierInterface extends TagsInterface
{
    /**
     * Modifier: `abstract`
     */
    public const int ABSTRACT = 16;

    /**
     * Modifier: `final`
     */
    public const int FINAL = 32;

    /**
     * Modifier: `private`
     */
    public const int PRIVATE = 4;

    /**
     * Modifier: `protected`
     */
    public const int PROTECTED = 2;

    /**
     * Modifier: `public`
     */
    public const int PUBLIC = 1;

    /**
     * Modifier: `static`
     */
    public const int STATIC = 8;

    /**
     * Modifier: `public`, `protected` and `private`
     */
    public const int VISIBILITY_MASK = 7; // 1 | 2 | 4

    /**
     * Check, if the modifier `abstract`.
     *
     * @return bool
     */
    public function isAbstract(): bool;

    /**
     * Check, if the modifier `final`.
     *
     * @return bool
     */
    public function isFinal(): bool;

    /**
     * Check, if the modifier `private`.
     *
     * @return bool
     */
    public function isPrivate(): bool;

    /**
     * Check, if the modifier `protected`.
     *
     * @return bool
     */
    public function isProtected(): bool;

    /**
     * Check, if the modifier `public`.
     *
     * @return bool
     */
    public function isPublic(): bool;

    /**
     * Check, if the modifier `static`.
     *
     * @return bool
     */
    public function isStatic(): bool;

    /**
     * Set the modifier.
     *
     * @param int $flag Flag (Class constants `ModifierInterface::*`)
     *
     * @return void
     */
    public function setModifier(int $flag): void;

    /**
     * Set the modifier from phpdoc tags
     */
    public function setModifiersFromTags(): void;
}
