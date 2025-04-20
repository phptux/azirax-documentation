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

ob_start();

$root = __DIR__ . DIRECTORY_SEPARATOR;
define('PROJECT_PATH', $root);
defined('PATH_CACHE') || define('PATH_CACHE', $root . '_output' . DIRECTORY_SEPARATOR . 'cache' . DIRECTORY_SEPARATOR);

require_once $root . '_config/functions.php';

loadIni();
loadAutoloader($root);
loadFolders();
loadDefined();

unset($root);
