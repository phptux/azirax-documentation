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

namespace Azirax\Documentation;

use Azirax\Documentation\Parser\Parser;
use Azirax\Documentation\Reflection\Interfaces\ClassReflectionInterface;
use Azirax\Documentation\Reflection\Interfaces\FunctionReflectionInterface;
use Azirax\Documentation\Reflection\LazyClassReflection;
use Azirax\Documentation\RemoteRepository\AbstractRemoteRepository;
use Azirax\Documentation\Renderer\Renderer;
use Azirax\Documentation\Store\StoreInterface;
use Azirax\Documentation\Version\SingleVersionCollection;
use Azirax\Documentation\Version\Version;
use Azirax\Documentation\Version\VersionCollection;
use InvalidArgumentException;
use LogicException;
use Symfony\Component\Filesystem\Filesystem;

use function array_filter;
use function array_key_exists;
use function array_keys;
use function array_merge;
use function call_user_func;
use function count;
use function dirname;
use function file_exists;
use function file_get_contents;
use function is_array;
use function is_dir;
use function is_string;
use function ksort;
use function ltrim;
use function rtrim;
use function sprintf;
use function str_contains;
use function str_replace;
use function strlen;
use function strrpos;
use function strtolower;
use function substr;
use function sys_get_temp_dir;

/**
 * Project class represents an API project.
 *
 * @package      Azirax\Documentation
 * @author       Rene Dziuba <php.tux@web.de>
 * @author       Fabien Potencier <fabien@symfony.com>
 * @copyright    Copyright (c) 2025 The Authors
 * @license      <https://opensource.org/license/mit> MIT License
 */
class Project
{
    /**
     * Array with loaded classes
     *
     * @var ClassReflectionInterface[]
     */
    protected array $classes = [];

    /**
     * Configuration array.
     *
     * @var array
     */
    protected array $config = [];

    /**
     * Symfony filesystem object
     *
     * @var Filesystem
     */
    protected Filesystem $filesystem;

    /**
     * Array with loaded functions.
     *
     * @var array
     */
    protected array $functions = [];

    /**
     * Array with loaded interfaces
     *
     * @var ClassReflectionInterface[]
     */
    protected array $interfaces = [];

    /**
     * Array with namespace classes
     *
     * @var array
     */
    protected array $namespaceClasses = [];

    /**
     * Array with namespace exceptions
     *
     * @var array
     */
    protected array $namespaceExceptions = [];

    /**
     * Array with namespace interfaces
     *
     * @var array
     */
    protected array $namespaceInterfaces = [];

    /**
     * Array with namespaces
     *
     * @var array
     */
    protected array $namespaces = [];

    /**
     * Parser object
     *
     * @var Parser|null
     */
    protected ?Parser $parser = null;

    /**
     * Array with PHP's internal classes.
     *
     * Lazy loaded from file `./resources/data/internal-classes.php`
     *
     * @var array|null
     */
    protected static ?array $phpInternalClasses = null;

    /**
     * Renderer object
     *
     * @var Renderer|null
     */
    protected ?Renderer $renderer = null;

    /**
     * Source directory
     *
     * @var string|null
     */
    protected ?string $sourceDir = null;

    /**
     * Store object
     *
     * @var StoreInterface
     */
    protected StoreInterface $store;

    /**
     * Version number
     *
     * @var string|null
     */
    protected ?string $version = null;

    /**
     * Version object.
     *
     * @var VersionCollection
     */
    protected VersionCollection $versions;

    /**
     * Constructor for Project
     *
     * @param StoreInterface         $store    Store object
     * @param VersionCollection|null $versions Version object
     * @param array                  $config   Configuration array
     */
    public function __construct(StoreInterface $store, ?VersionCollection $versions = null, array $config = [])
    {
        if (null === $versions) {
            $versions = new SingleVersionCollection(new Version(Azirax::$defaultVersionName));
        }
        $this->versions   = $versions;
        $this->store      = $store;
        $this->config     = array_merge(
            [
                'build_dir'           => sys_get_temp_dir() . 'azirax/build',
                'cache_dir'           => sys_get_temp_dir() . 'azirax/cache',
                'include_parent_data' => true,
                'theme'               => 'default',
            ],
            $config,
        );
        $this->filesystem = new Filesystem();

        if (count($this->versions) > 1) {
            foreach (['build_dir', 'cache_dir'] as $dir) {
                if (!str_contains($this->config[$dir], '%version%')) {
                    throw new LogicException(
                        sprintf(
                            'The "%s" setting must have the "%%version%%" placeholder'
                            . ' as the project has more than one version.',
                            $dir,
                        ),
                    );
                }
            }
        }

        $this->initialize();
    }

    /**
     * Add a class.
     *
     * @param ClassReflectionInterface $class Class reflection object
     *
     * @return void
     */
    public function addClass(ClassReflectionInterface $class): void
    {
        $this->classes[$class->getName()] = $class;
        $class->setProject($this);

        if ($class->isProjectClass()) {
            $this->updateCache($class);
        }
    }

    /**
     * Add a function.
     *
     * @param FunctionReflectionInterface $fun Function reflection object
     *
     * @return void
     */
    public function addFunction(FunctionReflectionInterface $fun): void
    {
        $this->functions[$fun->getNamespace()][$fun->getName()] = $fun;
        $fun->setProject($this);
        $this->addNamespace($fun->getNamespace() ?? '');
    }

    /**
     * Add namespace.
     *
     * @param string $namespace Namespace name
     *
     * @return void
     */
    public function addNamespace(string $namespace): void
    {
        $this->namespaces[$namespace] = $namespace;
        // add sub-namespaces

        while ($namespace = substr($namespace, 0, (int)strrpos($namespace, '\\'))) {
            $this->namespaces[$namespace] = $namespace;
        }
    }

    /**
     * Flush the cache directory.
     *
     * @param string $dir Cache directory
     *
     * @return void
     */
    public function flushDir(string $dir): void
    {
        $this->filesystem->remove($dir);
        $this->filesystem->mkdir($dir);
        file_put_contents($dir . '/AZIRAX_VERSION', Azirax::getVersion());
        file_put_contents($dir . '/PROJECT_VERSION', $this->version);
    }

    /**
     * Returns the base url.
     *
     * @return string|null
     */
    public function getBaseUrl(): ?string
    {
        $url = $this->getConfig('base_url');
        return $url === null ? null : rtrim($url, '/');
    }

    /**
     * Returns the build directory.
     *
     * @return string
     */
    public function getBuildDir(): string
    {
        return $this->prepareDir($this->config['build_dir']);
    }

    public function getCacheDir(): string
    {
        return $this->prepareDir($this->config['cache_dir']);
    }

    /**
     * Returns the Class Reflection object from the class.
     *
     * @param string $name Class name
     *
     * @return ClassReflectionInterface
     */
    public function getClass(string $name): ClassReflectionInterface
    {
        $name = ltrim($name, '\\');

        if (isset($this->classes[$name])) {
            return $this->classes[$name];
        }

        $class = new LazyClassReflection($name);
        $this->addClass($class);

        return $class;
    }

    /**
     * Returns a value from the configuration.
     *
     * @param string     $name    Configuration name
     * @param mixed|null $default Default return value
     *
     * @return mixed
     */
    public function getConfig(string $name, mixed $default = null): mixed
    {
        return $this->config[$name] ?? $default;
    }

    /**
     * Returns the footer link data.
     *
     * @return array
     */
    public function getFooterLink(): array
    {
        $link = $this->getConfig('footer_link');

        return [
            'href'        => $link['href'] ?? '',
            'target'      => $link['target'] ?? '',
            'rel'         => $link['rel'] ?? '',
            'before_text' => $link['before_text'] ?? '',
            'link_text'   => $link['link_text'] ?? '',
            'after_text'  => $link['after_text'] ?? '',
        ];
    }

    /**
     * Returns all classes, interface and exception classes from a namespace.
     *
     * @param string $namespace Namespace name
     *
     * @return ClassReflectionInterface[]
     */
    public function getNamespaceAllClasses(string $namespace): array
    {
        $classes = array_merge(
            $this->getNamespaceExceptions($namespace),
            $this->getNamespaceInterfaces($namespace),
            $this->getNamespaceClasses($namespace),
        );

        ksort($classes);

        return $classes;
    }

    /**
     * Returns all classes from a namespace.
     *
     * @param string $namespace Namespace name
     *
     * @return ClassReflectionInterface[]
     */
    public function getNamespaceClasses(string $namespace): array
    {
        if (!isset($this->namespaceClasses[$namespace])) {
            return [];
        }

        ksort($this->namespaceClasses[$namespace]);

        return $this->namespaceClasses[$namespace];
    }

    /**
     * Returns all exception classes from a namespace.
     *
     * @param string $namespace Namespace name
     *
     * @return ClassReflectionInterface[]
     */
    public function getNamespaceExceptions(string $namespace): array
    {
        if (!isset($this->namespaceExceptions[$namespace])) {
            return [];
        }

        ksort($this->namespaceExceptions[$namespace]);

        return $this->namespaceExceptions[$namespace];
    }

    /**
     * Returns all functions from a namespace.
     *
     * @param string $namespace Namespace name
     *
     * @return FunctionReflectionInterface[]
     */
    public function getNamespaceFunctions(string $namespace): array
    {
        if (!isset($this->functions[$namespace])) {
            return [];
        }

        ksort($this->functions[$namespace]);

        return $this->functions[$namespace];
    }

    /**
     * Returns all interfaces from a namespace.
     *
     * @param string $namespace Namespace name
     *
     * @return ClassReflectionInterface[]
     */
    public function getNamespaceInterfaces(string $namespace): array
    {
        if (!isset($this->namespaceInterfaces[$namespace])) {
            return [];
        }

        ksort($this->namespaceInterfaces[$namespace]);

        return $this->namespaceInterfaces[$namespace];
    }

    /**
     * Returns all sub namespaces.
     *
     * @param string $parent Parent namespace name
     *
     * @return array
     */
    public function getNamespaceSubNamespaces(string $parent): array
    {
        $prefix     = strlen($parent) ? ($parent . '\\') : '';
        $len        = strlen($prefix);
        $namespaces = [];

        foreach ($this->namespaces as $sub) {
            $prefixMatch = substr($sub, 0, $len) === $prefix;
            if ($prefixMatch && !str_contains(substr($sub, $len), '\\')) {
                $namespaces[] = $sub;
            }
        }

        return $namespaces;
    }

    /**
     * Returns all namespaces.
     *
     * @return array
     */
    public function getNamespaces(): array
    {
        ksort($this->namespaces);

        return array_keys($this->namespaces);
    }

    /**
     * Returns all project classes.
     *
     * @return array
     */
    public function getProjectClasses(): array
    {
        $classes = array_filter($this->classes, function ($class) {
            return $class->isProjectClass() && $class->isClass();
        });
        ksort($classes);

        return $classes;
    }

    /**
     * Returns all project enums.
     *
     * @return array
     */
    public function getProjectEnums(): array
    {
        $enums = array_filter($this->classes, function ($class) {
            return $class->isProjectClass() && $class->isEnum();
        });
        ksort($enums);

        return $enums;
    }

    /**
     * Returns all project traits.
     *
     * @return array
     */
    public function getProjectTraits(): array
    {
        $traits = array_filter($this->classes, function ($class) {
            return $class->isProjectClass() && $class->isTrait();
        });
        ksort($traits);

        return $traits;
    }

    /**
     * Returns all function from the project.
     *
     * @return FunctionReflectionInterface[]
     */
    public function getProjectFunctions(): array
    {
        $functions = [];

        foreach ($this->functions as $allFunctionsOfNamespace) {
            foreach ($allFunctionsOfNamespace as $functionInNamespace) {
                $functions[] = $functionInNamespace;
            }
        }

        usort(
            $functions,
            static function (FunctionReflectionInterface $a, FunctionReflectionInterface $b): int {
                return strcmp($a->__toString(), $b->__toString());
            },
        );

        return $functions;
    }

    /**
     * Returns all interfaces from the project.
     *
     * @return ClassReflectionInterface[]
     */
    public function getProjectInterfaces(): array
    {
        $interfaces = [];
        foreach ($this->interfaces as $interface) {
            if ($interface->isProjectClass()) {
                $interfaces[$interface->getName()] = $interface;
            }
        }
        ksort($interfaces);

        return $interfaces;
    }

    /**
     * Returns the source directory.
     *
     * @return string
     */
    public function getSourceDir(): string
    {
        return $this->replaceVars($this->sourceDir);
    }

    /**
     * Returns the source root path.
     *
     * @return string|null
     */
    public function getSourceRoot(): ?string
    {
        $root = $this->getConfig('source_url');
        if (!$root) {
            return null;
        }

        if (str_contains($root, 'github')) {
            return $root . '/tree/' . $this->version;
        }

        return null;
    }

    /**
     * Returns the version string.
     *
     * @return string|null
     */
    public function getVersion(): ?string
    {
        return $this->version;
    }

    /**
     * Returns all registered versions.
     *
     * @return Version[]
     */
    public function getVersions(): array
    {
        return $this->versions->getVersions();
    }

    /**
     * Returns the view source url.
     *
     * @param string $relativePath Relative path
     * @param int|null    $line         Line number
     *
     * @return string
     */
    public function getViewSourceUrl(string $relativePath, int|null $line): string
    {
        $remoteRepository = $this->getConfig('remote_repository');

        if ($remoteRepository instanceof AbstractRemoteRepository) {
            return $remoteRepository->getFileUrl($this->version ?? '', $relativePath, $line);
        }

        return '';
    }

    /**
     * Check of a footer link.
     *
     * @return bool
     */
    public function hasFooterLink(): bool
    {
        return $this->getConfig('footer_link') !== null && is_array($this->getConfig('footer_link'));
    }

    /**
     * Check, if a namespace exists.
     *
     * @param string $namespace Namespace name
     *
     * @return bool
     */
    public function hasNamespace(string $namespace): bool
    {
        return array_key_exists($namespace, $this->namespaces);
    }

    /**
     * Check of namespaces.
     *
     * @return bool
     */
    public function hasNamespaces(): bool
    {
        // if there is only one namespace and this is the global one,
        // it means that there is no namespace in the project
        return [''] !== array_keys($this->namespaces);
    }

    /**
     * Initialize the project.
     *
     * @return void
     */
    public function initialize(): void
    {
        $this->namespaces          = [];
        $this->interfaces          = [];
        $this->classes             = [];
        $this->namespaceClasses    = [];
        $this->namespaceInterfaces = [];
        $this->namespaceExceptions = [];
    }

    /**
     * Check, if they class a PHP internal class.
     *
     * @param string $name Class name
     *
     * @return bool
     */
    public static function isPhpInternalClass(string $name): bool
    {
        if (self::$phpInternalClasses === null) {
            $filename                 = dirname(__DIR__) . '/resources/data/internal-classes.php';
            self::$phpInternalClasses = include($filename);
        }

        return isset(self::$phpInternalClasses[strtolower($name)]);
    }

    /**
     * Load a class from the store.
     *
     * This must only be used in LazyClassReflection to get the right values
     *
     * @param string $name Class name
     *
     * @return ClassReflectionInterface|null
     */
    public function loadClass(string $name): ?ClassReflectionInterface
    {
        $name = ltrim($name, '\\');

        if ($this->getClass($name) instanceof LazyClassReflection) {
            try {
                $this->addClass($this->store->readClass($this, $name));
            } catch (InvalidArgumentException $e) {
                // probably a PHP built-in class
                return null;
            }
        }

        return $this->classes[$name];
    }

    /**
     * Parse the API.
     *
     * @param callable|null $callback Callback function
     * @param bool          $force    Force flag
     *
     * @return void
     */
    public function parse(?callable $callback = null, bool $force = false): void
    {
        $previous = null;

        foreach ($this->versions as $version) {
            $this->switchVersion($version, $callback, $force);
            $this->parseVersion($version, $previous, $callback, $force);
            $previous = $this->getCacheDir();
        }
    }

    /**
     * Read the project from the store.
     *
     * @return void
     */
    public function read(): void
    {
        $this->initialize();

        foreach ($this->store->readProject($this) as $classOrFun) {
            if ($classOrFun instanceof FunctionReflectionInterface) {
                $this->addFunction($classOrFun);
            } elseif ($classOrFun instanceof ClassReflectionInterface) {
                $this->addClass($classOrFun);
            }
        }
    }

    /**
     * Remove a class from the project.
     *
     * @param ClassReflectionInterface $class Class reflection object
     *
     * @return void
     */
    public function removeClass(ClassReflectionInterface $class): void
    {
        unset($this->classes[$class->getName()]);
        unset($this->interfaces[$class->getName()]);
        unset($this->namespaceClasses[$class->getNamespace()][$class->getName()]);
        unset($this->namespaceInterfaces[$class->getNamespace()][$class->getName()]);
        unset($this->namespaceExceptions[$class->getNamespace()][$class->getName()]);
    }

    /**
     * Render the API.
     *
     * @param callable|null $callback Callback function
     * @param bool          $force    Force flag
     *
     * @return void
     */
    public function render(?callable $callback = null, bool $force = false): void
    {
        $previous = null;
        foreach ($this->versions as $version) {
            // here, we don't want to flush the parse cache, as we are rendering
            $this->switchVersion($version, $callback, false);
            $this->renderVersion($version, $previous, $callback, $force);
            $previous = $this->getBuildDir();
        }
    }

    /**
     * Seed the cache.
     *
     * @param string $previous Previous version
     * @param string $current  Current version
     *
     * @return void
     */
    public function seedCache(string $previous, string $current): void
    {
        $this->filesystem->remove($current);
        $this->filesystem->mirror($previous, $current);
        $this->read();
    }

    /**
     * Set the Parser object.
     *
     * @param Parser $parser Parser object.
     *
     * @return void
     */
    public function setParser(Parser $parser): void
    {
        $this->parser = $parser;
    }

    /**
     * Set the Renderer object.
     *
     * @param Renderer $renderer Renderer object
     *
     * @return void
     */
    public function setRenderer(Renderer $renderer): void
    {
        $this->renderer = $renderer;
    }

    /**
     * Set the source directory.
     *
     * @param string $sourceDir Source directory.
     *
     * @return void
     */
    public function setSourceDir(string $sourceDir): void
    {
        $this->sourceDir = $sourceDir;
    }

    /**
     * Switch the version.
     *
     * @param Version       $version  Version object
     * @param callable|null $callback Callback function
     * @param bool          $force    Force flag
     *
     * @return void
     */
    public function switchVersion(Version $version, ?callable $callback = null, bool $force = false): void
    {
        if (null !== $callback) {
            call_user_func($callback, Message::SWITCH_VERSION, $version);
        }

        $this->version = $version->getName();

        $this->initialize();

        if (!$force) {
            $this->read();
        }
    }

    /**
     * Update the generated API.
     *
     * @param callable|null $callback Callback function
     * @param bool          $force    Force flag
     *
     * @return void
     */
    public function update(?callable $callback = null, bool $force = false): void
    {
        foreach ($this->versions as $version) {
            $this->switchVersion($version, $callback, $force);
            $this->parseVersion($version, null, $callback, $force);
            $this->renderVersion($version, null, $callback, $force);
        }
    }

    /**
     * Parse the version.
     *
     * @param Version       $version  Version object
     * @param string|null   $previous Previous version
     * @param callable|null $callback Callback function
     * @param bool          $force    Force flag
     *
     * @return void
     */
    protected function parseVersion(Version $version, ?string $previous = null, ?callable $callback = null, bool $force = false): void
    {
        if ($this->parser === null) {
            throw new LogicException('You must set a parser.');
        }

        if ($version->isFrozen() && count($this->classes) > 0) {
            return;
        }

        if ($force) {
            $this->store->flushProject($this);
        }

        if ($previous && 0 === count($this->classes)) {
            $this->seedCache($previous, $this->getCacheDir());
        }

        $transaction = $this->parser->parse($this, $callback);

        if (null !== $callback) {
            call_user_func($callback, Message::PARSE_VERSION_FINISHED, $transaction);
        }
    }

    /**
     * Prepare cache directory.
     *
     * @param string $dir Cache directory
     *
     * @return string
     */
    protected function prepareDir(string $dir): string
    {
        static $prepared = [];

        $dir = $this->replaceVars($dir);

        if (isset($prepared[$dir])) {
            return $dir;
        }

        $prepared[$dir] = true;

        if (!is_dir($dir)) {
            $this->flushDir($dir);

            return $dir;
        }

        $aziraxVersion = null;
        if (file_exists($dir . '/AZIRAX_VERSION')) {
            $aziraxVersion = file_get_contents($dir . '/AZIRAX_VERSION');
        }

        if (Azirax::getVersion() !== $aziraxVersion) {
            $this->flushDir($dir);
        }

        return $dir;
    }

    /**
     * Render the version.
     *
     * @param Version       $version  Version object
     * @param string|null   $previous Previous version
     * @param callable|null $callback Callback function
     * @param bool          $force    Force flag
     *
     * @return void
     */
    protected function renderVersion(Version $version, ?string $previous = null, ?callable $callback = null, bool $force = false): void
    {
        if (null === $this->renderer) {
            throw new LogicException('You must set a renderer.');
        }

        $frozen = $version->isFrozen() && $this->renderer->isRendered($this) && $this->version === file_get_contents($this->getBuildDir() . '/PROJECT_VERSION');

        if ($force && !$frozen) {
            $this->flushDir($this->getBuildDir());
        }

        if ($previous && !$this->renderer->isRendered($this)) {
            $this->seedCache($previous, $this->getBuildDir());
        }

        $diff = $this->renderer->render($this, $callback, $force);

        if (null !== $callback) {
            call_user_func($callback, Message::RENDER_VERSION_FINISHED, $diff);
        }
    }

    /**
     * Replace variables.
     *
     * @param string|null $pattern Pattern
     *
     * @return string
     */
    protected function replaceVars(?string $pattern): string
    {
        if (is_string($pattern)) {
            return str_replace('%version%', (string)$this->version, $pattern);
        }

        return '';
    }

    /**
     * Update the cache.
     *
     * @param ClassReflectionInterface $class Class reflection object
     *
     * @return void
     */
    protected function updateCache(ClassReflectionInterface $class): void
    {
        $name = $class->getName();

        $this->addNamespace($class->getNamespace() ?? '');

        if ($class->isException()) {
            $this->namespaceExceptions[$class->getNamespace() ?? ''][$name] = $class;
        } elseif ($class->isInterface()) {
            $this->namespaceInterfaces[$class->getNamespace() ?? ''][$name] = $class;
            $this->interfaces[$name]                                        = $class;
        } else {
            $this->namespaceClasses[$class->getNamespace() ?? ''][$name] = $class;
        }
    }
}
