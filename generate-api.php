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
use Symfony\Component\Finder\Finder;

$iterator = Finder::create()
                  ->files()
                  ->name('*.php')
                  ->in([
                      __DIR__ . '/src',
                  ]);

return new Azirax($iterator, [
    'debug'             => false,
    'title'             => 'Azirax Documentation',
    'language'          => 'en',
    'theme'             => 'default',
    'buildDir'          => __DIR__ . '/api',
    'cacheDir'          => __DIR__ . '/cache',
    'includeParentData' => true,
    'todos'             => true
]);
