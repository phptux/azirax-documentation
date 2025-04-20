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

namespace Helper;

use Codeception\Module;
use PHPUnit\Framework\SkippedTestError;
use ReflectionClass;
use ReflectionException;

use function extension_loaded;
use function file_exists;
use function gc_collect_cycles;
use function glob;
use function is_dir;
use function is_file;
use function rmdir;
use function sprintf;
use function str_ends_with;
use function uniqid;
use function unlink;

/**
 * Unit
 *
 * @package    Helper
 * @author     Rene Dziuba <php.tux@web.de>
 * @copyright  Copyright (c) 2025 The Authors
 * @license    <https://opensource.org/licenses/MIT> MIT License
 */
class Unit extends Module
{
    /**
     * Checks if an extension is loaded and if not, skips the test
     *
     * @param string $extension The extension to check
     */
    public function checkExtensionIsLoaded(string $extension): void
    {
        if (true !== extension_loaded($extension)) {
            $this->skipTest(
                sprintf("Extension '%s' is not loaded. Skipping test", $extension)
            );
        }
    }

    /**
     * Throws the SkippedTestError exception to skip a test
     *
     * @param string $message The message to display
     */
    public function skipTest(string $message): void
    {
        throw new SkippedTestError($message);
    }

    /**
     * Returns a unique file name
     *
     * @param string $prefix A prefix for the file
     * @param string $suffix A suffix for the file
     *
     * @return string
     */
    public function getNewFileName(string $prefix = '', string $suffix = 'log'): string
    {
        $prefix = ($prefix) ? $prefix . '_' : '';
        $suffix = ($suffix) ? $suffix : 'log';

        return uniqid($prefix, true) . '.' . $suffix;
    }

    /**
     * @param string $directory
     */
    public function safeDeleteDirectory(string $directory): void
    {
        $files = glob($directory . '*', GLOB_MARK);
        foreach ($files as $file) {
            if (str_ends_with($file, '/')) {
                $this->safeDeleteDirectory($file);
            } else {
                unlink($file);
            }
        }

        if (is_dir($directory)) {
            rmdir($directory);
        }
    }

    /**
     * @param string $filename
     */
    public function safeDeleteFile(string $filename): void
    {
        if (file_exists($filename) && is_file($filename)) {
            gc_collect_cycles();
            unlink($filename);
        }
    }

    /**
     * @throws ReflectionException
     */
    public function getProtectedProperty(object|string $obj, string $prop): mixed
    {
        $reflection = new ReflectionClass($obj);

        $property = $reflection->getProperty($prop);

        return $property->getValue($obj);
    }

    /**
     * @throws ReflectionException
     */
    public function setProtectedProperty(object|string $obj, string $prop, mixed $value): void
    {
        $reflection = new ReflectionClass($obj);

        $property = $reflection->getProperty($prop);
        $property->setValue($obj, $value);

        $this->assertSame(
            $value,
            $property->getValue($obj)
        );
    }
}
