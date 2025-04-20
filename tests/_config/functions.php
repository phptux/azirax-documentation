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

use Codeception\Util\Autoload;

/*******************************************************************************
 * Load settings and setup
 *******************************************************************************/
/**
 * Initialize ini values and xdebug if it is loaded
 */
if (!function_exists('loadAutoloader')) {
    function loadAutoloader(string $root): void
    {

    }
}

/**
 * Converts ENV variables to define for tests to work
 */
if (!function_exists('loadDefined')) {
    function loadDefined(): void
    {
        if (!defined('PATH_DATA')) {
            define('PATH_DATA', dataDir());
        }

        if (!defined('PATH_OUTPUT')) {
            define('PATH_OUTPUT', outputDir());
        }
    }
}

/**
 * Ensures that certain folders are always ready for us.
 */
if (!function_exists('loadFolders')) {
    function loadFolders(): void
    {
        $folders = [
            'cache',
            'files',
            'logs',
        ];

        foreach ($folders as $folder) {
            $item = outputDir($folder);

            if (true !== file_exists($item)) {
                mkdir($item, 0777, true);
            }
        }
    }
}

/**
 * Initialize ini values and xdebug if it is loaded
 */
if (!function_exists('loadIni')) {
    function loadIni(): void
    {
        error_reporting(-1);

        ini_set('display_errors', '1');
        ini_set('display_startup_errors', '1');

        setlocale(LC_ALL, 'en_US.utf-8');

        date_default_timezone_set('UTC');

        if (function_exists('mb_internal_encoding')) {
            mb_internal_encoding('utf-8');
        }

        if (function_exists('mb_substitute_character')) {
            mb_substitute_character('none');
        }

        clearstatcache();

        if (extension_loaded('xdebug')) {
            ini_set('xdebug.cli_color', '1');
            ini_set('xdebug.dump_globals', 'On');
            ini_set('xdebug.show_local_vars', 'On');
            ini_set('xdebug.max_nesting_level', '100');
            ini_set('xdebug.var_display_max_depth', '4');
        }
    }
}

/*******************************************************************************
 * Directories
 *******************************************************************************/
/**
 * Returns the cache folder
 */
if (!function_exists('cacheDir')) {
    function cacheDir(string $fileName = ''): string
    {
        return codecept_output_dir() . $fileName;
    }
}

/**
 * Returns the output folder
 */
if (!function_exists('dataDir')) {
    function dataDir(string $fileName = ''): string
    {
        return codecept_data_dir() . $fileName;
    }
}

/**
 * Returns the output folder
 */
if (!function_exists('outputDir')) {
    function outputDir(string $fileName = ''): string
    {
        return codecept_output_dir() . $fileName;
    }
}

/**
 * Returns the logs folder
 */
if (!function_exists('logsDir')) {
    function logsDir(string $fileName = ''): string
    {
        return codecept_output_dir()
            . 'logs' . DIRECTORY_SEPARATOR
            . $fileName;
    }
}

/*******************************************************************************
 * Utility
 *******************************************************************************/
if (!function_exists('env')) {
    function env(string $key, $default = null)
    {
        if (defined($key)) {
            return constant($key);
        }

        if (isset($_ENV[$key])) {
            return $_ENV[$key];
        }

        if (isset($_SERVER[$key])) {
            return $_SERVER[$key];
        }

        return $default;
    }
}

if (!function_exists('defineFromEnv')) {
    function defineFromEnv(string $name): void
    {
        if (defined($name)) {
            return;
        }

        define(
            $name,
            env($name)
        );
    }
}
