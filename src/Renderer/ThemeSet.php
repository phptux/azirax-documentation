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

namespace Azirax\Documentation\Renderer;

use InvalidArgumentException;
use LogicException;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Yaml\Yaml;

use function file_get_contents;
use function sprintf;

/**
 * Manage the themes.
 *
 * @package      Azirax\Documentation\Renderer
 * @author       Rene Dziuba <php.tux@web.de>
 * @author       Fabien Potencier <fabien@symfony.com>
 * @copyright    Copyright (c) 2025 The Authors
 * @license      <https://opensource.org/license/mit> MIT License
 */
class ThemeSet
{
    /**
     * Array with themes.
     *
     * @var Theme[]
     */
    protected array $themes = [];

    /**
     * Constructor for ThemeSet
     *
     * @param array $dirs Array with themes directories
     */
    public function __construct(array $dirs)
    {
        $this->discover($dirs);
    }

    /**
     * Returns a theme object.
     *
     * @param string $name Theme name
     *
     * @return Theme
     */
    public function getTheme(string $name): Theme
    {
        if (!isset($this->themes[$name])) {
            throw new InvalidArgumentException(sprintf('Theme "%s" does not exist.', $name));
        }

        return $this->themes[$name];
    }

    /**
     * Load the themes from the directories.
     *
     * @param array $dirs Array with themes directories
     *
     * @return void
     */
    protected function discover(array $dirs): void
    {
        $this->themes = [];
        $parents      = [];

        foreach (Finder::create()->name('manifest.yml')->in($dirs) as $manifest) {
            $manifest = $manifest->getPathname();
            $text   = file_get_contents($manifest);
            $config = Yaml::parse($text);
            if (!isset($config['name'])) {
                throw new InvalidArgumentException(sprintf('Theme manifest in "%s" must have a "name" entry.', $manifest));
            }

            $this->themes[$config['name']] = $theme = new Theme($config['name'], dirname($manifest));

            if (isset($config['parent'])) {
                $parents[$config['name']] = $config['parent'];
            }

            foreach (['static', 'global', 'namespace', 'class'] as $type) {
                if (isset($config[$type])) {
                    $theme->setTemplates($type, $config[$type]);
                }
            }
        }

        // populate parent
        foreach ($parents as $name => $parent) {
            if (!isset($this->themes[$parent])) {
                throw new LogicException(sprintf('Theme "%s" inherits from an unknown "%s" theme.', $name, $parent));
            }

            $this->themes[$name]->setParent($this->themes[$parent]);
        }
    }
}
