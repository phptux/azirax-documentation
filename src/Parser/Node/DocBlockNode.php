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

namespace Azirax\Documentation\Parser\Node;

use function explode;
use function is_string;

/**
 * Doc block node class.
 *
 * @package      Azirax\Documentation\Parser\Node
 * @author       Rene Dziuba <php.tux@web.de>
 * @author       Fabien Potencier <fabien@symfony.com>
 * @copyright    Copyright (c) 2025 The Authors
 * @license      <https://opensource.org/license/mit> MIT License
 */
class DocBlockNode
{
    /**
     * Array with errors
     *
     * @var array
     */
    protected array $errors = [];

    /**
     * Long description
     *
     * @var string|null
     */
    protected ?string $longDesc = null;

    /**
     * Short description
     *
     * @var string|null
     */
    protected ?string $shortDesc = null;

    /**
     * Array with tags
     *
     * @var array
     */
    protected array $tags = [];

    /**
     * Add an error message.
     *
     * @param string $error Error message
     *
     * @return void
     */
    public function addError(string $error): void
    {
        $this->errors[] = $error;
    }

    /**
     * Add a tag.
     *
     * @param string $key   Tag name
     * @param array|string  $value Tag data
     */
    public function addTag(string $key, array|string $value): void
    {
        $this->tags[$key][] = $value;
    }

    /**
     * Returns the full description.
     *
     * @return string
     */
    public function getDesc(): string
    {
        return $this->shortDesc . "\n\n" . $this->longDesc;
    }

    /**
     * Returns all error messages as an array.
     *
     * @return array
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * Returns the long description text.
     *
     * @return string|null
     */
    public function getLongDesc(): ?string
    {
        return $this->longDesc;
    }

    /**
     * Returns the data from other tags.
     *
     * @return array
     */
    public function getOtherTags(): array
    {
        $tags = $this->tags;
        unset($tags['param'], $tags['return'], $tags['var'], $tags['throws']);

        foreach ($tags as $name => $values) {
            foreach ($values as $i => $value) {
                // For 'see' tag we try to maintain backwards compatibility
                // by returning only a part of the value.
                if ($name === 'see') {
                    $value = $value[0];
                }

                $tags[$name][$i] = is_string($value) ? explode(' ', $value) : $value;
            }
        }

        return $tags;
    }

    /**
     * Returns the short description text.
     *
     * @return string|null
     */
    public function getShortDesc(): ?string
    {
        return $this->shortDesc;
    }

    /**
     * Returns the data from a tag.
     *
     * @param string $key Tag name
     *
     * @return array
     */
    public function getTag(string $key): array
    {
        return $this->tags[$key] ?? [];
    }

    /**
     * Returns all tag data.
     *
     * @return array
     */
    public function getTags(): array
    {
        return $this->tags;
    }

    /**
     * Set the long description text.
     *
     * @param string $longDesc Text
     */
    public function setLongDesc(string $longDesc): void
    {
        $this->longDesc = $longDesc;
    }

    /**
     * Set the short description text.
     *
     * @param string $shortDesc Text
     */
    public function setShortDesc(string $shortDesc): void
    {
        $this->shortDesc = $shortDesc;
    }
}
