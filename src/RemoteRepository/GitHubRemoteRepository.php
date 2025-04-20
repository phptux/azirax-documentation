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

/**
 * Remote repository class for GitHub.
 *
 * @package      Azirax\Documentation\RemoteRepository
 * @author       Rene Dziuba <php.tux@web.de>
 * @author       Fabien Potencier <fabien@symfony.com>
 * @copyright    Copyright (c) 2025 The Authors
 * @license      <https://opensource.org/license/mit> MIT License
 */
class GitHubRemoteRepository extends AbstractRemoteRepository
{
    /**
     * Returns the URL for a file.
     *
     * @param string   $projectVersion
     * @param string   $relativePath
     * @param int|null $line
     *
     * @return string
     */
    public function getFileUrl(string $projectVersion, string $relativePath, ?int $line = null): string
    {
        $url = 'https://github.com/' . $this->name . '/blob/' . $this->buildProjectPath($projectVersion, $relativePath);

        if (null !== $line) {
            $url .= '#L' . (int) $line;
        }

        return $url;
    }

}
