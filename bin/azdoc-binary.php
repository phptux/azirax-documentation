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

use Azirax\Documentation\Console\Application;

if (PHP_VERSION_ID < 80300) {// 80 3 00
    echo 'You need to use PHP 8.3 or above to run Azirax documentation.' . PHP_EOL;
    echo 'Current detected version: (' . PHP_MAJOR_VERSION . '.' . PHP_MINOR_VERSION . ') (' . PHP_VERSION_ID . ').' . PHP_EOL;
    exit(1);
}

// installed via composer?
$composerAutoLoadFile = __DIR__ . '/../../../autoload.php';

$composerAutoLoadFileEnv = getenv('AZIRAX_COMPOSER_AUTOLOAD_FILE');
if (is_string($composerAutoLoadFileEnv)) {
    $composerAutoLoadFile = $composerAutoLoadFileEnv;
}

if (file_exists($composerAutoLoadFile)) {
    require_once $composerAutoLoadFile;
} else {
    require_once __DIR__ . '/../vendor/autoload.php';
}


$application = new Application();
$application->run();
