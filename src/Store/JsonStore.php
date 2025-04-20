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

namespace Azirax\Documentation\Store;

use Azirax\Documentation\Project;
use Azirax\Documentation\Reflection\ClassReflection;
use Azirax\Documentation\Reflection\FunctionReflection;
use Exception;
use InvalidArgumentException;
use RuntimeException;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;

use function file_exists;
use function file_get_contents;
use function file_put_contents;
use function is_dir;
use function json_decode;
use function json_encode;
use function sprintf;
use function unlink;

use const JSON_PRETTY_PRINT;

/**
 * Store the project data in JSON format.
 *
 * @package      Azirax\Documentation\Store
 * @author       Rene Dziuba <php.tux@web.de>
 * @author       Fabien Potencier <fabien@symfony.com>
 * @copyright    Copyright (c) 2025 The Authors
 * @license      <https://opensource.org/license/mit> MIT License
 */
class JsonStore implements StoreInterface
{
    /**
     * Flag for JSON pretty print
     */
    private const int JSON_PRETTY_PRINT = JSON_PRETTY_PRINT;

    /**
     * Remove all project data from the store.
     *
     * @param Project $project Project object
     *
     * @return void
     */
    public function flushProject(Project $project): void
    {
        $filesystem = new Filesystem();
        $filesystem->remove($this->getStoreDir($project));
    }

    /**
     * Read the class data from the store.
     *
     * @param Project $project Project object
     * @param string  $name    Class name
     *
     * @return ClassReflection
     */
    public function readClass(Project $project, string $name): ClassReflection
    {
        $fileName = $this->getFilename('class', $project, $name);

        if (!file_exists($fileName)) {
            throw new InvalidArgumentException(sprintf('File "%s" for class "%s" does not exist.', $fileName, $name));
        }

        return ClassReflection::fromArray($project, $this->readJsonFile($fileName));
    }

    /**
     * Read the function data from the store.
     *
     * @param Project $project Project object
     * @param string  $name    Function name
     *
     * @return FunctionReflection
     */
    public function readFunction(Project $project, string $name): FunctionReflection
    {
        $fileName = $this->getFilename('function', $project, $name);

        if (!file_exists($fileName)) {
            throw new InvalidArgumentException(sprintf('File "%s" for function "%s" does not exist.', $fileName, $name));
        }

        return FunctionReflection::fromArray($project, $this->readJsonFile($fileName));
    }

    /**
     * Read the project from the store.
     *
     * @param Project $project Project object
     *
     * @return array
     */
    public function readProject(Project $project): array
    {
        $classesOrFunctions = [];

        // Classes
        $files = Finder::create()->name('c_*.json')->files()->in($this->getStoreDir($project));
        foreach ($files as $file) {
            $contents = file_get_contents($file->getPathname());
            if ($contents === false) {
                continue;
            }
            $data = json_decode($contents, true);
            if ($data === false || $data === null) {
                continue;
            }
            $classesOrFunctions[] = ClassReflection::fromArray($project, $data);
        }

        // Functions
        $files = Finder::create()->name('f_*.json')->files()->in($this->getStoreDir($project));
        foreach ($files as $file) {
            $contents = file_get_contents($file->getPathname());
            if ($contents === false) {
                continue;
            }
            $data = json_decode($contents, true);
            if ($data === false || $data === null) {
                continue;
            }
            $classesOrFunctions[] = FunctionReflection::fromArray($project, $data);
        }

        return $classesOrFunctions;
    }

    /**
     * Remove the class data from the store.
     *
     * @param Project $project Project object
     * @param string  $name    Class name
     *
     * @return void
     */
    public function removeClass(Project $project, string $name): void
    {
        if (!file_exists($this->getFilename('class', $project, $name))) {
            throw new RuntimeException(sprintf('Unable to remove the "%s" class.', $name));
        }

        unlink($this->getFilename('class', $project, $name));
    }

    /**
     * Remove the function data from the store.
     *
     * @param Project $project Project object
     * @param string  $name    Function name
     *
     * @return void
     */
    public function removeFunction(Project $project, string $name): void
    {
        if (!file_exists($this->getFilename('function', $project, $name))) {
            throw new RuntimeException(sprintf('Unable to remove the "%s" function.', $name));
        }

        unlink($this->getFilename('function', $project, $name));
    }

    /**
     * Write the class data in the store.
     *
     * @param Project         $project Project object
     * @param ClassReflection $class   Class object
     *
     * @return void
     */
    public function writeClass(Project $project, ClassReflection $class): void
    {
        file_put_contents(
            $this->getFilename('class', $project, $class->getName()),
            json_encode($class->toArray(), self::JSON_PRETTY_PRINT),
        );
    }

    /**
     * Write the function data in the store.
     *
     * @param Project            $project  Project object
     * @param FunctionReflection $function Function object
     *
     * @return void
     */
    public function writeFunction(Project $project, FunctionReflection $function): void
    {
        file_put_contents(
            $this->getFilename('function', $project, $function->getName()),
            json_encode($function->toArray(), self::JSON_PRETTY_PRINT),
        );
    }

    /**
     * Returns the filename.
     *
     * @param string  $type    File type (`class` or `function`)
     * @param Project $project Project object
     * @param string  $name    Class or function name
     *
     * @return string
     */
    protected function getFilename(string $type, Project $project, string $name): string
    {
        $dir = $this->getStoreDir($project);

        return $dir . '/' . $type[0] . '_' . md5($name) . '.json';
    }

    /**
     * Returns the store directory.
     *
     * @param Project $project Project object
     *
     * @return string
     */
    protected function getStoreDir(Project $project): string
    {
        $dir = $project->getCacheDir() . '/store';

        if (!is_dir($dir)) {
            $filesystem = new Filesystem();
            $filesystem->mkdir($dir);
        }

        return $dir;
    }

    /**
     * Read the JSON file.
     *
     * @param string $filePath JSON filename
     *
     * @return array
     * @throws Exception
     */
    protected function readJsonFile(string $filePath): array
    {
        $contents = file_get_contents($filePath);
        if ($contents === false) {
            throw new Exception(
                sprintf('Unable to read the class: %s', $filePath),
            );
        }

        $contents = json_decode($contents, true);
        if ($contents === false) {
            throw new Exception(
                sprintf('Unable to JSON decode the class from: %s', $filePath),
            );
        }

        return $contents;
    }
}
