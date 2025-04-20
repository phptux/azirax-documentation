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

use function array_replace;
use function array_unshift;

/**
 * Class for theme data.
 *
 * @package      Azirax\Documentation\Renderer
 * @author       Rene Dziuba <php.tux@web.de>
 * @author       Fabien Potencier <fabien@symfony.com>
 * @copyright    Copyright (c) 2025 The Authors
 * @license      <https://opensource.org/license/mit> MIT License
 */
class Theme
{
    /**
     * Theme directory
     *
     * @var string
     */
    protected string $dir;

    /**
     * Theme name
     *
     * @var string
     */
    protected string $name;

    /**
     * Theme object of the parent theme
     *
     * @var Theme|null
     */
    protected ?Theme $parent = null;

    /**
     * Array with templates.
     *
     * @var array
     */
    protected array $templates = [];

    /**
     * Constructor for Theme
     *
     * @param string $name Theme name
     * @param string $dir  Theme directory
     */
    public function __construct(string $name, string $dir)
    {
        $this->name = $name;
        $this->dir  = $dir;
    }

    /**
     * Returns the theme name.
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Returns the parent theme object.
     *
     * @return Theme|null
     */
    public function getParent(): ?Theme
    {
        return $this->parent;
    }

    /**
     * Returns the template directories.
     *
     * @return array
     */
    public function getTemplateDirs(): array
    {
        $dirs = [];

        if ($this->parent) {
            $dirs = $this->parent->getTemplateDirs();
        }

        array_unshift($dirs, $this->dir);

        return $dirs;
    }

    /**
     * Returns the templates.
     *
     * @param string $type Template name
     *
     * @return array
     */
    public function getTemplates(string $type): array
    {
        $templates = [];
        if ($this->parent) {
            $templates = $this->parent->getTemplates($type);
        }

        if (!isset($this->templates[$type])) {
            return $templates;
        }

        return array_replace($templates, $this->templates[$type]);
    }

    /**
     * Set the parent theme as an object.
     *
     * @param Theme $parent Theme object
     *
     * @return void
     */
    public function setParent(Theme $parent): void
    {
        $this->parent = $parent;
    }

    /**
     * Set the templates.
     *
     * @param string $type      Type name
     * @param array  $templates Array with templates
     *
     * @return void
     */
    public function setTemplates(string $type, array $templates): void
    {
        $this->templates[$type] = $templates;
    }
}
