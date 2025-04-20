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
 * Interface for php-doc tags.
 *
 * @package      Azirax\Documentation\Reflection\Interfaces
 * @author       Rene Dziuba <php.tux@web.de>
 * @author       Fabien Potencier <fabien@symfony.com>
 * @copyright    Copyright (c) 2025 The Authors
 * @license      <https://opensource.org/license/mit> MIT License
 */
interface TagsInterface
{
    /**
     * Returns the tag data for `category`.
     *
     * @return array
     */
    public function getCategories(): array;

    /**
     * Returns the tag data for `deprecated`.
     *
     * @return array
     */
    public function getDeprecated(): array;

    /**
     * Returns the tag data for `example`.
     *
     * @return array
     */
    public function getExamples(): array;

    /**
     * Returns all exceptions from the documentation.
     *
     * @return array
     */
    public function getExceptions(): array;

    /**
     * Returns the tag data for `internal`.
     *
     * @return array
     */
    public function getInternal(): array;

    /**
     * Returns all exceptions without formatted.
     *
     * @return array
     */
    public function getRawExceptions(): array;

    /**
     * Returns the `see` data.
     *
     * @return array[]
     */
    public function getSee(): array;

    /**
     * Returns the tag data for `since`.
     *
     * @return string|null
     */
    public function getSince(): ?string;

    /**
     * Returns the data from a tag.
     *
     * @param string $name Tag name
     *
     * @return array
     */
    public function getTags(string $name): array;

    /**
     * Returns the tag data for `todo`.
     *
     * @return array
     */
    public function getTodo(): array;

    /**
     * Check, if it has data for `categories`.
     *
     * @return bool
     */
    public function hasCategories(): bool;

    /**
     * Check, if it has data for `example`.
     *
     * @return bool
     */
    public function hasExamples(): bool;

    /**
     * Check, if the documentation has exceptions.
     *
     * @return bool
     */
    public function hasExceptions(): bool;

    /**
     * Check, if has an `since`tag.
     *
     * @return bool
     */
    public function hasSince(): bool;

    /**
     * Check, if it has data for `deprecated`.
     *
     * @return bool
     */
    public function isDeprecated(): bool;

    /**
     * Check, if it has data for `internal`.
     *
     * @return bool
     */
    public function isInternal(): bool;

    /**
     * Set the exceptions from the documentation.
     *
     * @param array $exceptions Exceptions as an array
     *
     * @return void
     */
    public function setExceptions(array $exceptions): void;

    /**
     * Set the tags.
     *
     * @param array $tags Array with tags
     */
    public function setTags(array $tags): void;

    /**
     * Set the `see` data.
     *
     * @param array $see Data
     */
    public function setSee(array $see): void;
}
