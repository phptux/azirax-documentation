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

namespace Azirax\Documentation\Tests\Source;

/**
 * Test class for parse.
 *
 * ```php
 * $testClass = new TestClass();
 * echo $testClass->id;
 * ```
 *
 * @package      Azirax\Documentation\Tests\Source
 * @author       Rene Dziuba <php.tux@web.de>
 * @author       Fabien Potencier <fabien@symfony.com>
 * @copyright    Copyright (c) 2025 The Authors
 * @license      <https://opensource.org/license/mit> MIT License
 */
class TestClass
{
    /**
     * Constant as string.
     */
    public const string CONST_STRING = 'const-string';

    /**
     * Constant as integer.
     */
    protected const int CONST_INT = 1;

    /**
     * Constant as float.
     */
    private const float CONST_FLOAT = 0.5;

    /**
     * ID
     *
     * @var int
     */
    public int $id = 12;

    /**
     * Name
     *
     * @var string|null
     */
    protected ?string $name = null;

    /**
     * Access array
     *
     * @var array
     */
    private array $access = [];

    /**
     * Test class constants object
     *
     * @var TestClassConstants|null
     */
    protected ?TestClassConstants $classes = null;

    /**
     * Returns the name.
     *
     * ```php
     * $testClass->setName('Rene');
     *
     * echo $testClass->getName();
     * ```
     *
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * Set the name.
     *
     * @param string $name  Name
     *
     * @return void
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }
}
