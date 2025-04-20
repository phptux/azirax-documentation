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

use function count;
use function implode;
use function is_array;

/**
 * Trait for php-doc tags.
 *
 * @package      Azirax\Documentation\Reflection\Traits
 * @author       Rene Dziuba <php.tux@web.de>
 * @author       Fabien Potencier <fabien@symfony.com>
 * @copyright    Copyright (c) 2025 The Authors
 * @license      <https://opensource.org/license/mit> MIT License
 */
trait TagsTrait
{
    /**
     * Array with tag data `throws`.
     *
     * @var array
     */
    protected array $exceptions = [];

    /**
     * Array with tag data `see`.
     *
     * @var array
     */
    protected array $see = [];

    /**
     * Array with tags.
     *
     * @var array
     */
    protected array $tags = [];

    /**
     * Returns the tag data for `category`.
     *
     * @return array
     */
    public function getCategories(): array
    {
        return $this->getTags('category');
    }

    /**
     * Returns the tag data for `deprecated`.
     *
     * @return array
     */
    public function getDeprecated(): array
    {
        return $this->getTags('deprecated');
    }

    /**
     * Returns the tag data for `example`.
     *
     * @return array
     */
    public function getExamples(): array
    {
        return $this->getTags('example');
    }

    /**
     * Returns all exceptions from the documentation.
     *
     * @return array
     */
    public function getExceptions(): array
    {
        $exceptions = [];

        foreach ($this->exceptions as $exception) {
            $exceptions[] = [
                $this->getClass()->getProject()->getClass(is_array($exception) ? $exception[0] : $exception),
                '',
            ];
        }

        return $exceptions;
    }

    /**
     * Returns the tag data for `internal`.
     *
     * @return array
     */
    public function getInternal(): array
    {
        return $this->getTags('internal');
    }

    /**
     * Returns all exceptions without formatted.
     *
     * @return array
     */
    public function getRawExceptions(): array
    {
        return $this->exceptions;
    }

    /**
     * Returns the `see` data.
     *
     * @return array[]
     */
    public function getSee(): array
    {
        $see     = [];
        $class   = $this->getClass();
        $project = $class->getProject();

        foreach ($this->see as $seeElem) {
            if ($seeElem[3]) {
                $seeElem = $this->prepareMethodSee($seeElem);
            } elseif ($seeElem[2]) {
                $seeElem[2] = $project->getClass($seeElem[2]);
            }

            $see[] = $seeElem;
        }
        return $see;
    }

    /**
     * Returns the tag data for `since`.
     *
     * @return string|null
     */
    public function getSince(): ?string
    {
        $sinceTags = $this->getTags('since');

        return count($sinceTags) > 0 ? implode(' ', $sinceTags[0]) : null;
    }

    /**
     * Returns the data from a tag.
     *
     * @param string $name Tag name
     *
     * @return array
     */
    public function getTags(string $name): array
    {
        return $this->tags[$name] ?? [];
    }

    /**
     * Returns the tag data for `todo`.
     *
     * @return array
     */
    public function getTodo(): array
    {
        return $this->getTags('todo');
    }

    /**
     * Check, if it has data for `categories`.
     *
     * @return bool
     */
    public function hasCategories(): bool
    {
        return !empty($this->getCategories());
    }

    /**
     * Check, if it has data for `example`.
     *
     * @return bool
     */
    public function hasExamples(): bool
    {
        return !empty($this->getExamples());
    }

    /**
     * Check, if the documentation has exceptions.
     *
     * @return bool
     */
    public function hasExceptions(): bool
    {
        return count($this->exceptions) > 0;
    }

    /**
     * Check, if has an `since`tag.
     *
     * @return bool
     */
    public function hasSince(): bool
    {
        return $this->getSince() !== null;
    }

    /**
     * Check, if it has data for `deprecated`.
     *
     * @return bool
     */
    public function isDeprecated(): bool
    {
        return !empty($this->getDeprecated());
    }

    /**
     * Check, if it has data for `internal`.
     *
     * @return bool
     */
    public function isInternal(): bool
    {
        return !empty($this->getTags('internal'));
    }

    /**
     * Set the exceptions from the documentation.
     *
     * @param array $exceptions Exceptions as an array
     *
     * @return void
     */
    public function setExceptions(array $exceptions): void
    {
        $this->exceptions = $exceptions;
    }

    /**
     * Set the `see` data.
     *
     * @param array $see Data
     */
    public function setSee(array $see): void
    {
        $this->see = $see;
    }

    /**
     * Set the tags.
     *
     * @param array $tags Array with tags
     */
    public function setTags(array $tags): void
    {
        $this->tags = $tags;
    }

    /**
     * Prepare the `see` data.
     *
     * @param array $seeElem Data
     *
     * @return array
     */
    private function prepareMethodSee(array $seeElem): array
    {
        $class   = $this->getClass();
        $project = $class->getProject();

        $method = null;

        if ($seeElem[2] !== false) {
            $class  = $project->getClass($seeElem[2]);
            $method = $class->getMethod($seeElem[3]);
        }

        if ($method) {
            $seeElem[2] = false;
            $seeElem[3] = $method;
        } else {
            $seeElem[2] = false;
            $seeElem[3] = false;
        }

        return $seeElem;
    }
}
