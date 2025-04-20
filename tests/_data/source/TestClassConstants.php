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
 * Test class for constants.
 *
 * @package      Azirax\Documentation\Tests\Source
 * @author       Rene Dziuba <php.tux@web.de>
 * @author       Fabien Potencier <fabien@symfony.com>
 * @copyright    Copyright (c) 2025 The Authors
 * @license      <https://opensource.org/license/mit> MIT License
 */
class TestClassConstants
{
    const array CONST_ARRAY
        = [
            'firstname' => 'Rene',
            'lastname'  => 'Dziuba',
        ];

    /**
     * Constant as float.
     */
    private const float CONST_FLOAT = 0.5;

    /**
     * Constant as integer.
     */
    protected const int CONST_INT = 1;

    /**
     * Constant as string.
     */
    public const string CONST_STRING = 'const-string';

    protected const string CONST_CONST = TestClass::CONST_STRING;
}
