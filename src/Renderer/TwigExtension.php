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

use Azirax\Documentation\Project;
use Azirax\Documentation\Reflection\Interfaces\ClassReflectionInterface;
use Azirax\Documentation\Reflection\Interfaces\FunctionReflectionInterface;
use Azirax\Documentation\Reflection\Interfaces\MethodReflectionInterface;
use Azirax\Documentation\Reflection\Interfaces\PropertyReflectionInterface;
use Azirax\Documentation\Reflection\Interfaces\ReflectionInterface;
use Azirax\Documentation\Tree;
use Parsedown;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

use function array_pop;
use function count;
use function explode;
use function htmlentities;
use function htmlspecialchars;
use function preg_match;
use function preg_replace_callback;
use function rtrim;
use function sprintf;
use function str_repeat;
use function str_replace;
use function strip_tags;

/**
 * Twig extensions.
 *
 * @package      Azirax\Documentation\Renderer
 * @author       Rene Dziuba <php.tux@web.de>
 * @author       Fabien Potencier <fabien@symfony.com>
 * @copyright    Copyright (c) 2025 The Authors
 * @license      <https://opensource.org/license/mit> MIT License
 */
class TwigExtension extends AbstractExtension
{
    /**
     * Current depth
     *
     * @var int|null
     */
    protected ?int $currentDepth = null;

    /**
     * Markdown object
     *
     * @var Parsedown|null
     */
    protected ?Parsedown $markdown = null;

    /**
     * Project object
     *
     * @var Project|null
     */
    protected ?Project $project = null;

    /**
     * Generate the abbreviation of a class
     *
     * @param ClassReflectionInterface|string $class    The class
     * @param bool                            $absolute Absolute flag
     *
     * @return string
     */
    public static function abbrClass(ClassReflectionInterface|string $class, bool $absolute = false): string
    {
        if ($class instanceof ClassReflectionInterface) {
            if (!$class->isProjectClass()) {
                return $class->getName();
            }

            $short = $class->getShortName();
            $class = $class->getName();

            if ($short === $class && !$absolute) {
                return htmlspecialchars($class, ENT_QUOTES);
            }
        } else {
            $parts = explode('\\', $class, ENT_QUOTES);

            if (count($parts) === 1 && !$absolute) {
                return htmlspecialchars($class);
            }

            $short = array_pop($parts);
        }

        return sprintf('<abbr title="%s">%s</abbr>', htmlentities($class, ENT_QUOTES), htmlspecialchars($short));
    }

    /**
     * Returns a list of filters to add to the existing list.
     *
     * @return TwigFilter[] An array of filters
     */
    public function getFilters(): array
    {
        return [
            new TwigFilter('desc', [$this, 'parseDesc'], ['needs_context' => false, 'is_safe' => ['html']]),
            new TwigFilter('md_to_html', [$this, 'markdownToHtml'], ['needs_context' => false, 'is_safe' => ['html']]),
            new TwigFilter('snippet', [$this, 'getSnippet']),
        ];
    }

    /**
     * Returns a list of functions to add to the existing list.
     *
     * @return TwigFunction[] An array of functions
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('global_namespace_name', [Tree::class, 'getGlobalNamespaceName']),
            new TwigFunction('global_namespace_page_name', [Tree::class, 'getGlobalNamespacePageName']),
            new TwigFunction('function_path', [$this, 'pathForFunction'], [
                'needs_context' => true,
                'is_safe'       => ['html'],
            ]),
            new TwigFunction('namespace_path', [$this, 'pathForNamespace'], [
                'needs_context' => true,
                'is_safe'       => ['html'],
            ]),
            new TwigFunction('class_path', [$this, 'pathForClass'], ['needs_context' => true, 'is_safe' => ['html']]),
            new TwigFunction('method_path', [$this, 'pathForMethod'], ['needs_context' => true, 'is_safe' => ['html']]),
            new TwigFunction('property_path', [$this, 'pathForProperty'], [
                'needs_context' => true,
                'is_safe'       => ['html'],
            ]),
            new TwigFunction('path', [$this, 'pathForStaticFile'], ['needs_context' => true]),
            new TwigFunction(
                'abbr_class',
                static function ($class, bool $absolute = false) {
                    return self::abbrClass($class, $absolute);
                },
                ['is_safe' => ['html']],
            ),
        ];
    }

    /**
     * Seems not to be used
     *
     * @param string $string Snippet
     *
     * @return string
     */
    public function getSnippet(string $string): string
    {
        if (preg_match('/^(.{50,}?)\s.*/m', $string, $matches)) {
            $string = $matches[1];
        }

        return str_replace(["\n", "\r"], '', strip_tags($string));
    }

    /**
     * Transform Markdown in description text to HTML.
     *
     * @param string|null $desc Description text
     *
     * @return string|null
     */
    public function markdownToHtml(?string $desc): ?string
    {
        if ($desc === null || $desc === '') {
            return $desc;
        }

        if (null === $this->markdown) {
            $this->markdown = new Parsedown();
        }

        $desc           = str_replace(['<code>', '</code>'], ['```', '```'], $desc);
        $outputMarkdown = $this->markdown->text($desc);

        $matches = [];
        // Values without a space do not need to be forced into a <p> tag
        if (preg_match('#^<p>(\S+)</p>$#', $outputMarkdown, $matches) === 1) {
            return $matches[1] ?? $outputMarkdown;
        }

        return $outputMarkdown;
    }

    /**
     * Parse the description text.
     *
     * @param string|null         $desc               Description text
     * @param ReflectionInterface $classOrFunctionRef Reflection object
     *
     * @return string|null
     */
    public function parseDesc(?string $desc, ReflectionInterface $classOrFunctionRef): ?string
    {
        if ($desc === null || $desc === '') {
            return $desc;
        }

        $desc = (string)preg_replace_callback(
            '/@see ([^ ]+)/', // Match until a whitespace is found
            function ($match) use (&$classOrFunctionRef): string {
                return $this->transformContentsIntoLinks($match[1], $classOrFunctionRef);
            },
            $desc,
        );

        return (string)preg_replace_callback(
            '/\{@link (?!\})(?<contents>[^\r\n\t\f]+)\}/',
            function (array $match) use (&$classOrFunctionRef): string {
                $data = rtrim($match['contents'], '}');
                return $this->transformContentsIntoLinks($data, $classOrFunctionRef);
            },
            $desc,
        );
    }

    /**
     * Returns the path for a class.
     *
     * @param array  $context Context
     * @param string $class   Class name
     *
     * @return string
     */
    public function pathForClass(array $context, string $class): string
    {
        return $this->relativeUri($this->currentDepth) . str_replace('\\', '/', $class) . '.html';
    }

    /**
     * Returns the path for a function.
     *
     * @param array                       $context  Context
     * @param FunctionReflectionInterface $function Function reflection object
     *
     * @return string
     */
    public function pathForFunction(array $context, FunctionReflectionInterface $function): string
    {
        $namespace = $this->pathForNamespace($context, $function->getNamespace());

        return $this->relativeUri($this->currentDepth) . $namespace . '#function_' . str_replace('\\', '', $function->getName());
    }

    /**
     * Returns the path for an class method.
     *
     * @param array                     $context Context
     * @param MethodReflectionInterface $method  Method reflection object
     *
     * @return string
     */
    public function pathForMethod(array $context, MethodReflectionInterface $method): string
    {
        $class = $method->getClass();

        return $this->relativeUri($this->currentDepth) . str_replace('\\', '/', $class->getName()) . '.html#method_' . $method->getName();
    }

    /**
     * Returns the path for an namespace.
     *
     * @param array  $context   Context
     * @param string $namespace Namespace name
     *
     * @return string
     */
    public function pathForNamespace(array $context, string $namespace): string
    {
        if ($namespace === '') {
            $namespace = Tree::getGlobalNamespacePageName();
        }
        return $this->relativeUri($this->currentDepth) . str_replace('\\', '/', $namespace) . '.html';
    }

    /**
     * Returns the path for a class property.
     *
     * @param array                       $context  Context
     * @param PropertyReflectionInterface $property Property reflection object
     *
     * @return string
     */
    public function pathForProperty(array $context, PropertyReflectionInterface $property): string
    {
        $class = $property->getClass();

        return $this->relativeUri($this->currentDepth) . str_replace('\\', '/', $class->getName()) . '.html#property_' . $property->getName();
    }

    /**
     * Returns the path for a static file.
     *
     * @param array  $context Context
     * @param string $file    Filename
     *
     * @return string
     */
    public function pathForStaticFile(array $context, string $file): string
    {
        return $this->relativeUri($this->currentDepth) . $file;
    }

    /**
     * Set the current depth.
     *
     * @param int $depth Depth
     *
     * @return void
     */
    public function setCurrentDepth(int $depth): void
    {
        $this->currentDepth = $depth;
    }

    /**
     * Transform content into links.
     *
     * @param string              $data                Data
     * @param ReflectionInterface $classOrFunctionRefl Reflection object
     *
     * @return string
     */
    public function transformContentsIntoLinks(string $data, ReflectionInterface $classOrFunctionRefl): string
    {
        $isClassReflection    = $classOrFunctionRefl instanceof ClassReflectionInterface;
        $isFunctionReflection = $classOrFunctionRefl instanceof FunctionReflectionInterface;
        if (!$isClassReflection && !$isFunctionReflection) {
            return $data;
        }

        /** @var ClassReflectionInterface|FunctionReflectionInterface $class */
        $class = $classOrFunctionRefl;

        // Example: Foo::bar_function_on_foo_class
        $classMethod = explode('::', trim($data, " \t\n\r"), 2);

        // Found "bar_function_on_foo_class", from example: bar_function_on_foo_class
        if (count($classMethod) === 1 && $class instanceof ClassReflectionInterface) {
            // In this case we resolve a link to a method name in the current class
            $method = $class->getMethod($classMethod[0]);
            if ($method !== null) {
                $short = $this->pathForMethod([], $method);
                return '[' . $data . '](' . $short . ')';
            }
        }

        /** The Original one is not realistic */
        $project = $class->getProject();
        if ($project === null) {
            // This should never happen
            return $data;
        }

        $cr = $project->getClass($classMethod[0]);
        if ($cr->isPhpClass()) {
            $className = $cr->getName();
            return '[' . $className . '](https://www.php.net/' . $className . ')';
        }

        if (!$cr->isProjectClass()) {
            return $data;
        }

        // Found "bar_function_on_foo_class", from example: Foo::bar_function_on_foo_class
        if (count($classMethod) === 2) {
            // In this case we have a function name to resolve on the previously found class
            $method = $cr->getMethod($classMethod[1]);
            if ($method !== false) {
                $short = $this->pathForMethod([], $method);
                return '[' . $data . '](' . $short . ')';
            }
        }

        // Final case, we link the found class
        $short = $this->pathForClass([], $cr->getName());

        return '[' . $data . '](' . $short . ')';
    }

    /**
     * Returns the relative Uri.
     *
     * @param int|null $value Value
     *
     * @return string
     */
    protected function relativeUri(?int $value): string
    {
        if (!$value) {
            return '';
        }

        return rtrim(str_repeat('../', $value), '/') . '/';
    }
}
