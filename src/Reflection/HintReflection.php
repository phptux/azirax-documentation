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

namespace Azirax\Documentation\Reflection;

use Azirax\Documentation\Reflection\Interfaces\ClassReflectionInterface;

/**
 * Reflection for an hint.
 *
 * @package      Azirax\Documentation\Reflection
 * @author       Rene Dziuba <php.tux@web.de>
 * @author       Fabien Potencier <fabien@symfony.com>
 * @copyright    Copyright (c) 2025 The Authors
 * @license      <https://opensource.org/license/mit> MIT License
 */
class HintReflection
{
    /**
     * Hint is an array?
     *
     * @var bool
     */
    protected bool $isArray;

    /**
     * Hint name
     *
     * @var string
     */
    protected string $name;

    /**
     * Reflection class object.
     *
     * @var ClassReflectionInterface|null
     */
    protected ?ClassReflectionInterface $class = null;

    /**
     * Constructor for HintReflection
     *
     * @param ClassReflectionInterface|string $name    Hint name or ClassReflectionInterface object
     * @param bool|null                   $isArray Hint is an array?
     */
    public function __construct(ClassReflectionInterface|string $name, bool|null $isArray)
    {
        if ($name instanceof ClassReflectionInterface) {
            $this->name  = (string)$name;
            $this->class = $name;
        } else {
            $this->name = $name;
        }

        $this->isArray = (bool) $isArray;
    }

    /**
     * Returns the hint name.
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->getName();
    }

    /**
     * Returns the hint name.
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Check, if they hint an array?
     *
     * @return bool
     */
    public function isArray(): bool
    {
        return $this->isArray;
    }

    /**
     * Check, if the hint names a ClassReflectionInterface object.
     *
     * @return bool
     */
    public function isClass(): bool
    {
        return $this->class !== null;
    }

    /**
     * Returns the class reflection object.
     *
     * @return ClassReflectionInterface|null
     */
    public function getClass(): ?ClassReflectionInterface
    {
        return $this->class;
    }

    /**
     * Marked the hint as an array or not.
     *
     * @param bool $flag Flag
     *
     * @return void
     */
    public function markAsArray(bool $flag): void
    {
        $this->isArray = $flag;
    }

    /**
     * Set the hint name.
     *
     * @param ClassReflectionInterface|string $name Hint name or ClassReflectionInterface object
     */
    public function setName(ClassReflectionInterface|string $name): void
    {
        $this->name = $name;
    }
}
