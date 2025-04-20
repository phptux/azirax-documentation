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

use Azirax\Documentation\Reflection\Interfaces\ModifierInterface;

use function count;
use function implode;
use function strtolower;
use function trim;

/**
 * Trait for modifiers.
 *
 * @package      Azirax\Documentation\Reflection\Traits
 * @author       Rene Dziuba <php.tux@web.de>
 * @author       Fabien Potencier <fabien@symfony.com>
 * @copyright    Copyright (c) 2025 The Authors
 * @license      <https://opensource.org/license/mit> MIT License
 */
trait ModifierTrait
{
    /**
     * Modifier type.
     *
     * @var int
     */
    protected int $modifier = 0;

    /**
     * Check, if the modifier `abstract`.
     *
     * @return bool
     */
    public function isAbstract(): bool
    {
        return ModifierInterface::ABSTRACT === (ModifierInterface::ABSTRACT & $this->modifier);
    }

    /**
     * Check, if the modifier `final`.
     *
     * @return bool
     */
    public function isFinal(): bool
    {
        return ModifierInterface::FINAL === (ModifierInterface::FINAL & $this->modifier);
    }

    /**
     * Check, if the modifier `private`.
     *
     * @return bool
     */
    public function isPrivate(): bool
    {
        return ModifierInterface::PRIVATE === (ModifierInterface::PRIVATE & $this->modifier);
    }

    /**
     * Check, if the modifier `protected`.
     *
     * @return bool
     */
    public function isProtected(): bool
    {
        return ModifierInterface::PROTECTED === (ModifierInterface::PROTECTED & $this->modifier);
    }

    /**
     * Check, if the modifier `public`.
     *
     * @return bool
     */
    public function isPublic(): bool
    {
        return ModifierInterface::PUBLIC === (ModifierInterface::PUBLIC & $this->modifier);
    }

    /**
     * Check, if the modifier `static`.
     *
     * @return bool
     */
    public function isStatic(): bool
    {
        return ModifierInterface::STATIC === (ModifierInterface::STATIC & $this->modifier);
    }

    /**
     * Set the modifier.
     *
     * @param int $flag Flag (Class constants `ModifierInterface::*`)
     *
     * @return void
     */
    public function setModifier(int $flag): void
    {
        $this->modifier = $flag;
    }

    /**
     * Set the modifier from phpdoc tags
     */
    public function setModifiersFromTags(): void
    {
        $hasFinalTag     = count($this->getTags('final')) > 0;
        $hasProtectedTag = count($this->getTags('protected')) > 0;
        $hasPrivateTag   = count($this->getTags('private')) > 0;
        $hasPublicTag    = count($this->getTags('public')) > 0;
        $hasStaticTag    = count($this->getTags('static')) > 0;
        $accessTags      = $this->getTags('access');
        $hasAccessTag    = count($accessTags) > 0;
        $flags           = $this->modifiers ?? 0;

        if ($hasAccessTag) {
            $accessTag = strtolower(trim(implode('', $accessTags[0])));
            if ($accessTag === 'protected') {
                $hasProtectedTag = true;
            } elseif ($accessTag === 'private') {
                $hasPrivateTag = true;
            } elseif ($accessTag === 'public') {
                $hasPublicTag = true;
            }
        }

        if ($hasFinalTag) {
            $flags |= ModifierInterface::FINAL;
        }
        if ($hasProtectedTag) {
            $flags |= ModifierInterface::PROTECTED;
        }
        if ($hasPrivateTag) {
            $flags |= ModifierInterface::PRIVATE;
        }
        if ($hasPublicTag) {
            $flags |= ModifierInterface::PUBLIC;
        }
        if ($hasStaticTag) {
            $flags |= ModifierInterface::STATIC;
        }

        if ($flags > 0) {
            $this->setModifier($flags);
        }
    }
}
