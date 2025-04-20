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

use Azirax\Documentation\Project;
use Azirax\Documentation\Store\ArrayStore;
use Azirax\Documentation\Store\JsonStore;
use Azirax\Documentation\Store\StoreInterface;
use Azirax\Documentation\Version\VersionCollection;
use UnitTester;

class GetServiceCest
{
    /**
     * Tests Azirax\Documentation\Azirax :: getService()
     *
     * @param UnitTester $I
     *
     * @return void
     *
     * @author Azirax Team <php.tux@web.de>
     * @since  2025-04-05
     */
    public function aziraxGetService(UnitTester $I): void
    {
        $I->wantToTest('azirax - getService()');

        $azirax = $I->getAzirax(true);

        // Store
        $store = $azirax->getService('store');
        $I->assertInstanceOf(StoreInterface::class, $store);
        $I->assertInstanceOf(ArrayStore::class, $store);

        $azirax = $I->getAzirax(false);

        // Store
        $store = $azirax->getService('store');
        $I->assertInstanceOf(StoreInterface::class, $store);
        $I->assertInstanceOf(JsonStore::class, $store);

        // Versions
        $versions = $azirax->getService('versions');
        $I->assertInstanceOf(VersionCollection::class, $versions);

        // Project
        $project = $azirax->getService('project');
        $I->assertInstanceOf(Project::class, $project);
    }
}
