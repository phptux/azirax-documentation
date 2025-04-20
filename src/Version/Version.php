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

/**
 * Data class for a version.
 *
 * @package      Azirax\Documentation\Version
 * @author       Rene Dziuba <php.tux@web.de>
 * @author       Fabien Potencier <fabien@symfony.com>
 * @copyright    Copyright (c) 2025 The Authors
 * @license      <https://opensource.org/license/mit> MIT License
 */
class Version
{
    /**
     * Version is frozen?
     *
     * @var bool
     */
    protected bool $isFrozen;

    /**
     * Version long name
     *
     * @var string|null
     */
    protected ?string $longName;

    /**
     * Version name
     *
     * @var string
     */
    protected string $name;

    /**
     * Constructor for Version
     *
     * @param string      $name     Version name
     * @param string|null $longName Version long name
     */
    public function __construct(string $name, ?string $longName = null)
    {
        $this->name     = $name;
        $this->longName = null === $longName ? $name : $longName;
        $this->isFrozen = false;
    }

    /**
     * Returns the version name.
     *
     * Magic method to string.
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->name;
    }

    /**
     * Returns the long version name.
     *
     * @return string|null
     */
    public function getLongName(): ?string
    {
        return $this->longName;
    }

    /**
     * Returns the version name.
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Returns is the version frozen?
     *
     * @return bool
     */
    public function isFrozen(): bool
    {
        return $this->isFrozen;
    }

    /**
     * Mark the version as frozen or not.
     *
     * @param bool $isFrozen Flag
     *
     * @return void
     */
    public function setFrozen(bool $isFrozen): void
    {
        $this->isFrozen = $isFrozen;
    }

    /**
     * Set the version name.
     *
     * @param string $name Name
     *
     * @return void
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }
}
