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

namespace Azirax\Documentation\Version;

use Closure;
use RuntimeException;
use Symfony\Component\Finder\Glob;
use Symfony\Component\Process\Process;

use function array_filter;
use function array_unshift;
use function explode;
use function preg_match;
use function stripos;
use function trim;
use function usort;
use function version_compare;

/**
 * Git version collection.
 *
 * @package      Azirax\Documentation\Version
 * @author       Rene Dziuba <php.tux@web.de>
 * @author       Fabien Potencier <fabien@symfony.com>
 * @copyright    Copyright (c) 2025 The Authors
 * @license      <https://opensource.org/license/mit> MIT License
 */
class GitVersionCollection extends VersionCollection
{
    /**
     * Filter function
     *
     * @var Closure
     */
    protected Closure $filter;

    /**
     * Git path
     *
     * @var string
     */
    protected string $gitPath;

    /**
     * Repo URL
     *
     * @var string
     */
    protected string $repo;

    /**
     * Sort function
     *
     * @var Closure
     */
    protected Closure $sorter;

    /**
     * Constructor for GitVersionCollection
     *
     * @param string $repo Repo URL
     */
    public function __construct(string $repo)
    {
        $this->repo    = $repo;
        $this->filter  = static function (string $version): bool {
            foreach (['PR', 'RC', 'BETA', 'ALPHA'] as $str) {
                if (stripos($version, $str) !== false) {
                    return false;
                }
            }

            return true;
        };
        $this->sorter  = static function (string $a, string $b): int {
            return version_compare($a, $b, '>') === true ? 1 : 0;
        };
        $this->gitPath = 'git';
    }

    /**
     * Add the versions from the tag.
     *
     * @param callable|string|null $filter Filter callback function
     *
     * @return $this
     */
    public function addFromTags(callable|string|null $filter = null): self
    {
        $tags = array_filter(explode("\n", $this->execute(['tag'])));

        $versions = array_filter($tags, $this->filter);
        if (null !== $filter) {
            if (!$filter instanceof Closure) {
                $regexes = [];
                foreach ((array)$filter as $f) {
                    $regexes[] = Glob::toRegex($f);
                }
                $filter = static function ($version) use ($regexes) {
                    foreach ($regexes as $regex) {
                        if (preg_match($regex, $version)) {
                            return true;
                        }
                    }

                    return false;
                };
            }

            $versions = array_filter($versions, $filter);
        }
        usort($versions, $this->sorter);

        foreach ($versions as $version) {
            $version = new Version($version);
            $version->setFrozen(true);
            $this->add($version);
        }

        return $this;
    }

    /**
     * Set the filter function for the version.
     *
     * @param Closure $filter Callback function
     *
     * @return void
     */
    public function setFilter(Closure $filter): void
    {
        $this->filter = $filter;
    }

    /**
     * Set the git path.
     *
     * @param string $path Path
     *
     * @return void
     */
    public function setGitPath(string $path): void
    {
        $this->gitPath = $path;
    }

    /**
     * Set the filter for sorting the versions.
     *
     * @param Closure $sorter Callback function
     *
     * @return void
     */
    public function setSorter(Closure $sorter): void
    {
        $this->sorter = $sorter;
    }

    /**
     * Execute the git command.
     *
     * @param array $arguments Array with arguments
     *
     * @return string
     */
    protected function execute(array $arguments): string
    {
        array_unshift($arguments, $this->gitPath);

        $process = new Process($arguments, $this->repo);
        $process->run();

        if (!$process->isSuccessful()) {
            throw new RuntimeException(sprintf('Unable to run the command (%s).', $process->getErrorOutput()));
        }

        return $process->getOutput();
    }

    /**
     * Switch to version.
     *
     * @param Version $version Version object
     *
     * @return void
     */
    protected function switchVersion(Version $version): void
    {
        $process = new Process(['git', 'status', '--porcelain', '--untracked-files=no'], $this->repo);
        $process->run();
        if (!$process->isSuccessful() || trim($process->getOutput())) {
            throw new RuntimeException(sprintf('Unable to switch to version "%s" as the repository is not clean.', $version));
        }

        $this->execute(['checkout', '-qf', (string)$version]);
    }
}
