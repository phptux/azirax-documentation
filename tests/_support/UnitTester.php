<?php

/*
 +------------------------------------------------------------------------+
 | Copyright (c) 2025 Azirax Team (http://mrversion.de)                   |
 +------------------------------------------------------------------------+
 | This source file is subject to the New BSD License that is bundled     |
 | with this package in the file LICENSE.txt.                             |
 |                                                                        |
 | <https://opensource.org/license/mit> MIT License                       |
 +------------------------------------------------------------------------+
 | Authors: Rene Dziuba <php.tux@web.de>                                  |
 +------------------------------------------------------------------------+
*/
declare(strict_types = 1);

use Azirax\Documentation\Azirax;
use Codeception\Actor;
use Codeception\Lib\Friend;
use Symfony\Component\Finder\Finder;

/**
 * Inherited Methods
 *
 * @method void wantToTest($text)
 * @method void wantTo($text)
 * @method void execute($callable)
 * @method void expectTo($prediction)
 * @method void expect($prediction)
 * @method void amGoingTo($argumentation)
 * @method void am($role)
 * @method void lookForwardTo($achieveValue)
 * @method void comment($description)
 * @method Friend haveFriend($name, $actorClass = null)
 *
 * @SuppressWarnings(PHPMD)
 */
class UnitTester extends Actor
{
    use _generated\UnitTesterActions;

    /**
     * Define custom actions here
     */

    public function getAzirax(bool $debug, ?string $section = null): Azirax
    {
        $dataDir = dataDir('source');
        $buildDir = outputDir('build');
        $cacheDir = outputDir('cache');

        if ($section) {
            $dataDir .= DIRECTORY_SEPARATOR . $section;
            $cacheDir .= DIRECTORY_SEPARATOR . $section;
            $buildDir .= DIRECTORY_SEPARATOR . $section;
        }

        $iterator = Finder::create()
                          ->files()
                          ->name('*.php')
                          ->in([
                              $dataDir,
                          ]);

        return new Azirax($iterator, [
            'debug'     => $debug,
            'buildDir'  => $buildDir,
            'cacheDir' => $cacheDir,
            'theme' => 'dark'
        ]);
    }
}
