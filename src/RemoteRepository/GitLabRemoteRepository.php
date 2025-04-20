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
 * Remote repository class for GitLab.
 *
 * @package      Azirax\Documentation\RemoteRepository
 * @author       Rene Dziuba <php.tux@web.de>
 * @author       Fabien Potencier <fabien@symfony.com>
 * @copyright    Copyright (c) 2025 The Authors
 * @license      <https://opensource.org/license/mit> MIT License
 */
class GitLabRemoteRepository extends AbstractRemoteRepository
{
    /**
     * GitLab separator
     *
     * @var string
     */
    protected string $separator = '/-/blob/';

    /**
     * GitLab URL
     *
     * @var string
     */
    protected string $url = 'https://gitlab.com/';

    /**
     * Constructor for GitLabRemoteRepository
     *
     * @param string      $name      Repository name
     * @param string      $localPath Locale Path
     * @param string|null $url       GitLab URL
     * @param string|null $separator GitLab separator
     */
    public function __construct(
        string $name,
        string $localPath,
        ?string $url = null,
        ?string $separator = null,
    ) {
        if ($url !== null) {
            $this->url = $url;
        }

        if ($separator !== null) {
            $this->separator = $separator;
        }

        parent::__construct($name, $localPath);
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
    public function getFileUrl(string $projectVersion, string $relativePath, ?int $line = null): string
    {
        $url = $this->url . $this->name . $this->separator . $this->buildProjectPath($projectVersion, $relativePath);

        if (null !== $line) {
            $url .= '#L' . (int)$line;
        }

        return $url;
    }
}
