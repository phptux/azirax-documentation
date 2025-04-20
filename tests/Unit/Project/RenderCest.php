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

class RenderCest
{
    /**
     * Tests Azirax\Documentation\Project :: render()
     *
     * @param UnitTester $I
     *
     * @return void
     *
     * @author Azirax Team <php.tux@web.de>
     * @since  2025-04-12
     */
    public function projectRender(UnitTester $I): void
    {
        $I->wantToTest('project - render()');

        $azirax = $I->getAzirax(false);
        $project = $azirax->getService('project');
        $project->setParser($azirax->getService('parser'));
        $azirax->getService('store')->flushProject($project);
        $project->parse();

        $project->render(null, true);
    }
}
