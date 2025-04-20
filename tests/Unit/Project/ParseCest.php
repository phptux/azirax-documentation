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

use UnitTester;

class ParseCest
{
    /**
     * Tests Azirax\Documentation\Project :: parse()
     *
     * @param UnitTester $I
     *
     * @return void
     *
     * @author Azirax Team <php.tux@web.de>
     * @since  2025-04-07
     */
    public function projectParse(UnitTester $I): void
    {
        $I->wantToTest('project - parse()');

        $azirax = $I->getAzirax(false);
        $project = $azirax->getService('project');
        $project->setParser($azirax->getService('parser'));
        $azirax->getService('store')->flushProject($project);

        $project->parse();
    }

    /**
     * Tests Azirax\Documentation\Project :: parse() - Enum
     *
     * @param UnitTester $I
     *
     * @return void
     *
     * @author Azirax Team <php.tux@web.de>
     * @since  2025-04-12
     */
    public function projectParseEnum(UnitTester $I): void
    {
        $I->wantToTest('project - parse() - Enum');

        $azirax = $I->getAzirax(false, 'Db');
        $project = $azirax->getService('project');
        $project->setParser($azirax->getService('parser'));
        $azirax->getService('store')->flushProject($project);

        $project->parse();
    }
}
