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

use Azirax\Documentation\Indexer;
use Azirax\Documentation\Message;
use Azirax\Documentation\Project;
use Azirax\Documentation\Reflection\Interfaces\ClassReflectionInterface;
use Azirax\Documentation\Reflection\Interfaces\MethodReflectionInterface;
use Azirax\Documentation\Tree;
use Azirax\Documentation\TreeNode;
use Symfony\Component\Filesystem\Filesystem;
use Twig\Environment;

use function array_push;
use function array_reverse;
use function array_unique;
use function array_values;
use function count;
use function dirname;
use function file_exists;
use function file_put_contents;
use function is_callable;
use function is_dir;
use function json_encode;
use function ksort;
use function sprintf;
use function str_repeat;
use function str_replace;
use function strtoupper;
use function substr;
use function substr_count;
use function uksort;

/**
 * Renderer
 *
 * @package      Azirax\Documentation\Renderer
 * @author       Rene Dziuba <php.tux@web.de>
 * @author       Fabien Potencier <fabien@symfony.com>
 * @copyright    Copyright (c) 2025 The Authors
 * @license      <https://opensource.org/license/mit> MIT License
 */
class Renderer
{
    /**
     * Diff cache filename.
     */
    protected const string DIFF_FILENAME = 'renderer.index';

    /**
     * Cache search index
     *
     * @var array
     */
    protected array $cachedSearchIndex = [];

    /**
     * Cache tree
     *
     * @var TreeNode[]
     */
    protected array $cachedTree = [];

    /**
     * Filesystem object
     *
     * @var Filesystem
     */
    protected Filesystem $filesystem;

    /**
     * Indexer object
     *
     * @var Indexer
     */
    protected Indexer $indexer;

    /**
     * Step
     *
     * @var int
     */
    protected int $step = 0;

    /**
     * Steps
     *
     * @var int
     */
    protected int $steps = 0;

    /**
     * Array with templates
     *
     * @var array
     */
    protected array $templates = [];

    /**
     * Current theme object
     *
     * @var Theme|null
     */
    protected ?Theme $theme = null;

    /**
     * Themes manager object
     *
     * @var ThemeSet
     */
    protected ThemeSet $themes;

    /**
     * Tree object
     *
     * @var Tree
     */
    protected Tree $tree;

    /**
     * Twig object
     *
     * @var Environment
     */
    protected Environment $twig;

    /**
     * Constructor for Renderer
     *
     * @param Environment $twig    Twig object
     * @param ThemeSet    $themes  Themes manager object
     * @param Tree        $tree    Tree object
     * @param Indexer     $indexer Indexer object
     */
    public function __construct(Environment $twig, ThemeSet $themes, Tree $tree, Indexer $indexer)
    {
        $this->twig       = $twig;
        $this->themes     = $themes;
        $this->tree       = $tree;
        $this->indexer    = $indexer;
        $this->filesystem = new Filesystem();
    }

    /**
     * Check, if the project rendered.
     *
     * @param Project $project Project object
     *
     * @return bool
     */
    public function isRendered(Project $project): bool
    {
        return $this->getDiff($project)->isAlreadyRendered();
    }

    /**
     * Render the given project.
     *
     * @param Project       $project  Project object
     * @param callable|null $callback Custom callback function for sort and rendering.
     * @param bool          $force    Force flag
     *
     * @return Diff
     */
    public function render(Project $project, ?callable $callback = null, bool $force = false): Diff
    {
        $cacheDir = $project->getCacheDir() . '/twig';
        $this->twig->setCache($cacheDir);

        if ($force) {
            $project->flushDir($cacheDir);
        }

        $diff = $this->getDiff($project);

        if ($diff->isEmpty()) {
            return $diff;
        }

        $this->steps = count($diff->getModifiedClasses()) + count($diff->getModifiedNamespaces()) + count($this->getTheme($project)->getTemplates('global')) + 1;
        $this->step  = 0;

        $this->theme = $this->getTheme($project);
        $dirs        = $this->theme->getTemplateDirs();
        // add parent directory to be able to extends the same template as the current one but in the parent theme
        foreach ($dirs as $dir) {
            $dirs[] = dirname($dir);
        }
        $this->twig->getLoader()->setPaths(array_unique($dirs));

        $this->twig->addGlobal('has_namespaces', $project->hasNamespaces());
        $this->twig->addGlobal('project', $project);

        $this->renderStaticTemplates($project, $callback);
        $this->renderGlobalTemplates($project, $callback);
        $this->renderNamespaceTemplates($diff->getModifiedNamespaces(), $project, $callback);
        $this->renderClassTemplates($diff->getModifiedClasses(), $project, $callback);

        // cleanup
        foreach ($diff->getRemovedClasses() as $class) {
            foreach ($this->theme->getTemplates('class') as $target) {
                $this->filesystem->remove(sprintf($target, str_replace('\\', '/', $class)));
            }
        }

        $diff->save();

        return $diff;
    }

    /**
     * Returns the current diff object from the given project.
     *
     * @param Project $project Project object
     *
     * @return Diff
     */
    protected function getDiff(Project $project): Diff
    {
        return new Diff($project, $project->getBuildDir() . '/' . self::DIFF_FILENAME);
    }

    /**
     * Returns the index from the given project.
     *
     * @param Project $project Project object
     *
     * @return array
     */
    protected function getIndex(Project $project): array
    {
        $items = [];

        foreach ($project->getProjectClasses() as $class) {
            $letter           = strtoupper(substr($class->getShortName(), 0, 1));
            $items[$letter][] = ['class', $class];

            foreach ($class->getProperties() as $property) {
                $letter           = strtoupper(substr($property->getName(), 0, 1));
                $items[$letter][] = ['property', $property];
            }

            foreach ($class->getMethods() as $method) {
                $letter           = strtoupper(substr($method->getName(), 0, 1));
                $items[$letter][] = ['method', $method];
            }
        }
        ksort($items);

        return $items;
    }

    /**
     * Returns the current theme object from the given project.
     *
     * @param Project $project Project object
     *
     * @return Theme
     */
    protected function getTheme(Project $project): Theme
    {
        return $this->themes->getTheme($project->getConfig('theme'));
    }

    /**
     * Returns the variables from the class reflection object.
     *
     * @param ClassReflectionInterface $class    Array with class reflection objects
     * @param Project                  $project  Project object
     * @param Callable|null            $callback Custom callback function for sort
     *
     * @return array
     */
    protected function getVariablesFromClassReflection(ClassReflectionInterface $class, Project $project, ?callable $callback = null): array
    {
        if (null !== $callback) {
            call_user_func($callback, Message::RENDER_PROGRESS, [
                'Class',
                $class->getName(),
                $this->step,
                $this->steps,
            ]);
        }

        $properties = $class->getProperties($project->getConfig('include_parent_data'));

        $sortProperties = $project->getConfig('sort_class_properties');
        if ($sortProperties) {
            if (is_callable($sortProperties)) {
                uksort($properties, $sortProperties);
            } else {
                ksort($properties);
            }
        }

        $methods = $class->getMethods($project->getConfig('include_parent_data'));

        $sortMethods = $project->getConfig('sort_class_methods');
        if ($sortMethods) {
            if (is_callable($sortMethods)) {
                uksort($methods, $sortMethods);
            } else {
                ksort($methods);
            }
        }

        $constants = $class->getConstants($project->getConfig('include_parent_data'));

        $sortConstants = $project->getConfig('sort_class_constants');
        if ($sortConstants) {
            if (is_callable($sortConstants)) {
                uksort($constants, $sortConstants);
            } else {
                ksort($constants);
            }
        }

        $traits = $class->getTraits($project->getConfig('include_parent_data'));

        $sortTraits = $project->getConfig('sort_class_traits');
        if ($sortTraits) {
            if (is_callable($sortTraits)) {
                uksort($traits, $sortTraits);
            } else {
                ksort($traits);
            }
        }

        $sortInterfaces = $project->getConfig('sort_class_interfaces');
        if ($sortInterfaces) {
            $class->sortInterfaces($sortInterfaces);
        }

        return [
            'class'      => $class,
            'properties' => $properties,
            'methods'    => $methods,
            'constants'  => $constants,
            'traits'     => $traits,
            'tree'       => $this->getTree($project),
        ];
    }

    /**
     * Render a class template.
     *
     * @param ClassReflectionInterface[] $classes  Array with class reflection objects
     * @param Project                    $project  Project object
     * @param Callable|null              $callback Custom callback function for sort
     *
     * @return void
     */
    protected function renderClassTemplates(array $classes, Project $project, ?callable $callback = null): void
    {
        foreach ($classes as $class) {
            $variables = $this->getVariablesFromClassReflection($class, $project, $callback);

            foreach ($this->theme->getTemplates('class') as $template => $target) {
                $this->save($project, sprintf($target, str_replace('\\', '/', $class->getName())), $template, $variables);
            }
        }
    }

    /**
     * Render global templates.
     *
     * @param Project       $project  Project object
     * @param callable|null $callback Custom callback function for rendering
     *
     * @return void
     */
    protected function renderGlobalTemplates(Project $project, ?callable $callback = null): void
    {
        $variables = [
            'namespaces'   => $project->getNamespaces(),
            'interfaces'   => $project->getProjectInterfaces(),
            'classes'      => $project->getProjectClasses(),
            'traits'       => $project->getProjectTraits(),
            'enums'        => $project->getProjectEnums(),
            'functions'    => $project->getProjectFunctions(),
            'items'        => $this->getIndex($project),
            'index'        => $this->indexer->getIndex($project),
            'tree'         => $this->getTree($project),
            'search_index' => $this->getSearchIndex($project),
        ];

        foreach ($this->theme->getTemplates('global') as $template => $target) {
            if (null !== $callback) {
                call_user_func($callback, Message::RENDER_PROGRESS, ['Global', $target, $this->step, $this->steps]);
            }

            $this->save($project, $target, $template, $variables);
        }
    }

    /**
     * Render the namespace templates.
     *
     * @param array         $namespaces Array with namespaces
     * @param Project       $project    Project object
     * @param callable|null $callback   Custom callback function for display namespace
     *
     * @return void
     */
    protected function renderNamespaceTemplates(array $namespaces, Project $project, ?callable $callback = null): void
    {
        foreach ($namespaces as $namespace) {
            $namespaceDisplayName = $namespace;
            $namespaceName        = $namespace;
            if ($namespace === '') {
                $namespaceDisplayName = Tree::getGlobalNamespaceName();
                $namespaceName        = Tree::getGlobalNamespacePageName();
            }

            if (null !== $callback) {
                call_user_func($callback, Message::RENDER_PROGRESS, [
                    'Namespace',
                    $namespaceDisplayName,
                    $this->step,
                    $this->steps,
                ]);
            }

            $variables = [
                'namespace'     => $namespace,
                'subnamespaces' => $project->getNamespaceSubNamespaces($namespace),
                'functions'     => $project->getNamespaceFunctions($namespace),
                'classes'       => $project->getNamespaceClasses($namespace),
                'interfaces'    => $project->getNamespaceInterfaces($namespace),
                'traits'        => $project->getNamespaceTraits($namespace),
                'enums'         => $project->getNamespaceEnums($namespace),
                'exceptions'    => $project->getNamespaceExceptions($namespace),
                'tree'          => $this->getTree($project),
            ];

            foreach ($this->theme->getTemplates('namespace') as $template => $target) {
                $this->save($project, sprintf($target, str_replace('\\', '/', $namespaceName)), $template, $variables);
            }
        }
    }

    /**
     * Render the static templates.
     *
     * @param Project       $project  Project object
     * @param callable|null $callback Custom callback function for rendering
     *
     * @return void
     */
    protected function renderStaticTemplates(Project $project, ?callable $callback = null): void
    {
        if (null !== $callback) {
            call_user_func($callback, Message::RENDER_PROGRESS, [
                'Static',
                'Rendering files',
                $this->step,
                $this->steps,
            ]);
        }

        $dirs = $this->theme->getTemplateDirs();
        foreach ($this->theme->getTemplates('static') as $template => $target) {
            foreach (array_reverse($dirs) as $dir) {
                if (file_exists($dir . '/' . $template)) {
                    $this->filesystem->copy($dir . '/' . $template, $project->getBuildDir() . '/' . $target);

                    continue 2;
                }
            }
        }
    }

    /**
     * Save the Twig output from a template.
     *
     * @param Project $project   Project object
     * @param string  $uri       Uri
     * @param string  $template  Template name
     * @param array   $variables Array with template variables
     *
     * @return void
     */
    protected function save(Project $project, string $uri, string $template, array $variables): void
    {
        $depth = substr_count($uri, '/');
        /** @var TwigExtension $twigExtension */
        $twigExtension = $this->twig->getExtension(TwigExtension::class);
        $twigExtension->setCurrentDepth($depth);
        $this->twig->addGlobal('root_path', str_repeat('../', $depth));

        $file = $project->getBuildDir() . '/' . $uri;

        if (!is_dir($dir = dirname($file))) {
            $this->filesystem->mkdir($dir);
        }

        file_put_contents($file, $this->twig->render($template, $variables));
    }

    /**
     * Returns the search index.
     *
     * @param Project $project Project object
     *
     * @return string
     */
    private function getSearchIndex(Project $project): string
    {
        $key = $project->getBuildDir();
        if (!isset($this->cachedSearchIndex[$key])) {
            $twigExtension = new TwigExtension();
            $items         = [];
            /** @var MethodReflectionInterface[] $methods */
            $methods = [];

            foreach ($project->getProjectFunctions() as $function) {
                $items[] = [
                    't' => 'F',
                    'n' => $function->__toString(),
                    'p' => $twigExtension->pathForFunction([], $function),
                    'd' => $twigExtension->markdownToHtml(
                        $twigExtension->parseDesc($function->getShortDesc(), $function),
                    ),
                ];
            }

            foreach ($project->getProjectClasses() as $class) {
                $classItem = [
                    't' => $class->isTrait() ? 'T' : 'C',
                    'n' => $class->__toString(),
                    'p' => $twigExtension->pathForClass([], $class->__toString()),
                    'd' => $twigExtension->markdownToHtml(
                        $twigExtension->parseDesc($class->getShortDesc(), $class),
                    ),
                ];
                if ($class->getNamespace() !== null) {
                    $classItem['f'] = [
                        'n' => $class->getNamespace() === '' ? Tree::getGlobalNamespaceName() : $class->getNamespace(),
                        'p' => $twigExtension->pathForNamespace([], $class->getNamespace()),
                    ];
                }
                $items[]      = $classItem;
                $classMethods = array_values($class->getMethods());
                if (count($classMethods) > 0) {
                    array_push($methods, ...$classMethods);
                }
            }

            foreach ($project->getProjectInterfaces() as $interface) {
                $nsItem = [
                    't' => 'I',
                    'n' => $interface->__toString(),
                    'p' => $twigExtension->pathForClass([], $interface->__toString()),
                ];
                if ($interface->getNamespace() !== null) {
                    $nsItem['f'] = [
                        'n' => $interface->getNamespace() === '' ? Tree::getGlobalNamespaceName() : $interface->getNamespace(),
                        'p' => $twigExtension->pathForNamespace([], $interface->getNamespace()),
                    ];
                }
                $items[]          = $nsItem;
                $interfaceMethods = array_values($interface->getMethods());
                if (count($interfaceMethods) > 0) {
                    array_push($methods, ...$interfaceMethods);
                }
            }

            foreach ($methods as $method) {
                $methodItem = [
                    't' => 'M',
                    'n' => $method->__toString(),
                    'p' => $twigExtension->pathForMethod([], $method),
                    'd' => $twigExtension->markdownToHtml(
                        $twigExtension->parseDesc($method->getShortDesc(), $method),
                    ),
                ];
                if ($method->getClass() !== null) {
                    $nsItem['f'] = [
                        'n' => $method->getClass()->__toString(),
                        'p' => $twigExtension->pathForClass([], $method->getClass()->__toString()),
                    ];
                }
                $items[] = $methodItem;
            }

            foreach ($project->getNamespaces() as $namespace) {
                $items[] = [
                    't' => 'N',
                    'n' => $namespace,
                    'p' => $twigExtension->pathForNamespace([], $namespace),
                ];
            }

            $this->cachedSearchIndex[$key] = [
                'items' => $items,
            ];
        }

        return (string)json_encode(
            $this->cachedSearchIndex[$key],
            JSON_UNESCAPED_SLASHES,
        );
    }

    /**
     * Returns the tree for the given project.
     *
     * @param Project $project Project object
     *
     * @return string
     */
    private function getTree(Project $project): string
    {
        $key = $project->getBuildDir();
        if (!isset($this->cachedTree[$key])) {
            $this->cachedTree[$key] = [
                'tree'          => $this->tree->getTree($project),
                'treeOpenLevel' => $project->getConfig('defaultOpenedLevel'),
            ];
        }

        return (string)json_encode(
            $this->cachedTree[$key],
            JSON_UNESCAPED_SLASHES,
        );
    }
}
