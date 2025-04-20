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

namespace Azirax\Documentation\Tests\Unit\Azirax;

use Codeception\Example;
use UnitTester;

use function dataDir;
use function getcwd;
use function outputDir;

use const DIRECTORY_SEPARATOR;

class GetConfigCest
{
    /**
     * Tests Azirax\Documentation\Azirax :: getConfig()
     *
     * @dataProvider getExamples
     *
     * @param UnitTester $I
     * @param Example    $example
     *
     * @return void
     *
     * @author Azirax Team <php.tux@web.de>
     * @since  2025-04-06
     */
    public function aziraxGetConfig(UnitTester $I, Example $example): void
    {
        $I->wantToTest('azirax - getConfig() - ' . $example[0]);

        $azirax = $I->getAzirax(false);

        $actual = $azirax->getConfig($example[0]);
        $I->assertSame($example[1], $actual);
    }

    private function getExamples(): array
    {
        return [
            ['debug', false],
            ['version', 'main'],
            ['buildDir', outputDir('build')],
            ['cacheDir', outputDir('cache')],
            ['sourceDir', getcwd() . DIRECTORY_SEPARATOR]
        ];
    }
}
