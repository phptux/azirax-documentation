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

namespace Azirax\Documentation\RemoteRepository;

use function ltrim;
use function str_replace;

/**
 * Abstract remote repository class.
 *
 * @package      Azirax\Documentation\RemoteRepository
 * @author       Rene Dziuba <php.tux@web.de>
 * @author       Fabien Potencier <fabien@symfony.com>
 * @copyright    Copyright (c) 2025 The Authors
 * @license      <https://opensource.org/license/mit> MIT License
 */
abstract class AbstractRemoteRepository
{
    /**
     * Locale path
     *
     * @var string
     */
    protected string $localPath;

    /**
     * Repository name
     *
     * @var string
     */
    protected string $name;

    /**
     * Constructor for AbstractRemoteRepository
     *
     * @param string $name      Repository name
     * @param string $localPath Locale path
     */
    public function __construct(string $name, string $localPath)
    {
        $this->name      = $name;
        $this->localPath = $localPath;
    }

    /**
     * Returns the URL for a file.
     *
     * @param string   $projectVersion
     * @param string   $relativePath
     * @param int|null $line
     *
     * @return string
     */
    abstract public function getFileUrl(string $projectVersion, string $relativePath, ?int $line = null): string;

    /**
     * Returns the relative path.
     *
     * @param string $file Filename
     *
     * @return string
     */
    public function getRelativePath(string $file): string
    {
        $replacementCount = 0;
        $filePath         = str_replace($this->localPath, '', $file, $replacementCount);

        if (1 === $replacementCount) {
            return $filePath;
        }

        return '';
    }

    /**
     * Returns the build project path.
     *
     * @param string $projectVersion Project version
     * @param string $relativePath   Relative path
     *
     * @return string
     */
    protected function buildProjectPath(string $projectVersion, string $relativePath): string
    {
        return str_replace('\\', '/', $projectVersion . '/' . ltrim($relativePath, '/'));
    }
}
