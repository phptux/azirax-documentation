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

namespace Azirax\Documentation\Tests\Unit\Project;

use Azirax\Documentation\Tests\Source\TestClass;
use UnitTester;

class LoadClassCest
{
    /**
     * Tests Azirax\Documentation\Project :: loadClass()
     *
     * @param UnitTester $I
     *
     * @return void
     *
     * @author Azirax Team <php.tux@web.de>
     * @since  2025-04-06
     */
    public function projectLoadClass(UnitTester $I): void
    {
        $I->wantToTest('project - loadClass()');

        $azirax = $I->getAzirax(false);
        $project = $azirax->getService('project');
        $className = TestClass::class;

        $class = $project->loadClass($className);
        $I->assertSame($className, $class->getName());

        $expected = [
            [
                '<https://opensource.org/license/mit>',
                'MIT',
                'License'
            ]
        ];
        $actual = $class->getTags('license');
        $I->assertSame($expected, $actual);
    }
}
