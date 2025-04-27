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

use Azirax\Documentation\Providers\CodeParserProvider;
use Azirax\Documentation\Providers\DocBlockParserProvider;
use Azirax\Documentation\Providers\FilterProvider;
use Azirax\Documentation\Providers\IndexerProvider;
use Azirax\Documentation\Providers\ParserContextProvider;
use Azirax\Documentation\Providers\ParserProvider;
use Azirax\Documentation\Providers\PhpParserProvider;
use Azirax\Documentation\Providers\PhpTraverserProvider;
use Azirax\Documentation\Providers\PrettyPrinterProvider;
use Azirax\Documentation\Providers\ProjectProvider;
use Azirax\Documentation\Providers\RendererProvider;
use Azirax\Documentation\Providers\StoreProvider;
use Azirax\Documentation\Providers\ThemesProvider;
use Azirax\Documentation\Providers\TraverserProvider;
use Azirax\Documentation\Providers\TreeProvider;
use Azirax\Documentation\Providers\TwigProvider;
use Azirax\Documentation\Providers\VersionsProvider;
use Azirax\Documentation\RemoteRepository\AbstractRemoteRepository;
use Closure;
use InvalidArgumentException;
use Symfony\Component\Finder\Finder;

use function array_key_exists;
use function array_merge;
use function getcwd;
use function sprintf;

use const DIRECTORY_SEPARATOR;

/**
 * Azirax core class.
 *
 * @package      Azirax\Documentation
 * @author       Rene Dziuba <php.tux@web.de>
 * @author       Fabien Potencier <fabien@symfony.com>
 * @copyright    Copyright (c) 2025 The Authors
 * @license      <https://opensource.org/license/mit> MIT License
 */
class Azirax
{
    /**
     * Version: Dev
     */
    public const bool VERSION_DEV = false;

    /**
     * Version: Major
     */
    public const int VERSION_MAJOR = 1;

    /**
     * Version: Minor
     */
    public const int VERSION_MINOR = 1;

    /**
     * Version: Patch
     */
    public const int VERSION_PATCH = 0;

    /**
     * Configuration array.
     *
     * @var array
     */
    private array $config
        = [
            'version'   => null,
            'debug'     => false,
            'buildDir'  => null,
            'cacheDir'  => null,
            'sourceDir' => null,
        ];

    /**
     * Version default name
     *
     * @var string
     */
    public static string $defaultVersionName = 'main';

    /**
     * Iterator for API files.
     *
     * @var Finder|null
     */
    private ?Finder $files = null;

    /**
     * Remote repository object.
     *
     * @var AbstractRemoteRepository|null
     */
    private ?AbstractRemoteRepository $remoteRepository = null;

    /**
     * Array with service instances.
     *
     * @var object[]
     */
    private array $serviceInstances = [];

    /**
     * Array with services.
     *
     * @var Closure[]
     */
    private array $services = [];

    /**
     * Constructor for Azirax
     *
     * @param Finder|null $iterator Finder object
     * @param array       $config   Configuration array
     */
    public function __construct(?Finder $iterator = null, array $config = [])
    {
        $this->files      = $iterator;
        $serviceProviders = $this->getServiceProviders();

        if (isset($config['remoteRepository'])) {
            if ($config['remoteRepository'] instanceof AbstractRemoteRepository) {
                $this->remoteRepository = $config['remoteRepository'];
            }
            unset($config['remoteRepository']);
        }

        $this->config['version']            = self::$defaultVersionName;
        $this->config['buildDir']           = getcwd() . DIRECTORY_SEPARATOR . 'build';
        $this->config['cacheDir']           = getcwd() . DIRECTORY_SEPARATOR . 'cache';
        $this->config['sourceDir']          = getcwd() . DIRECTORY_SEPARATOR;
        $this->config['privateFilter']      = 1;
        $this->config['language']           = 'en';
        $this->config['title']              = 'API';
        $this->config['theme']              = 'default';
        $this->config['sortProperties']     = false;
        $this->config['sortMethods']        = false;
        $this->config['sortConstants']      = false;
        $this->config['sortTraits']         = false;
        $this->config['sortEnums']          = false;
        $this->config['sortInterfaces']     = false;
        $this->config['todos']              = false;
        $this->config['favicon']            = null;
        $this->config['includeParentData']  = true;
        $this->config['defaultOpenedLevel'] = 2;
        $this->config['sourceUrl']          = '';
        $this->config['baseUrl']            = null;
        $this->config['footerLink']         = [];
        $this->config['templateDirs']       = [];
        $this->config                       = array_merge($this->config, $config);

        foreach ($serviceProviders as $provider) {
            $instance = new $provider();
            $instance->register($this);
        }
    }

    /**
     * Add service.
     *
     * @param string $name    Service name
     * @param object $service Closure object for create the service or object
     *
     * @return void
     */
    public function addService(string $name, object $service): void
    {
        $this->services[$name] = $service;
    }

    /**
     * Returns a value from the configuration key.
     *
     * @param string     $name    Configuration key name
     * @param mixed|null $default Default return value
     *
     * @return mixed
     */
    public function getConfig(string $name, mixed $default = null): mixed
    {
        if (array_key_exists($name, $this->config)) {
            return $this->config[$name];
        }

        return $default;
    }

    /**
     * Returns the iterator for API files.
     *
     * @return Finder
     */
    public function getFiles(): Finder
    {
        return $this->files;
    }

    /**
     * Returns the remote repository object.
     *
     * @return AbstractRemoteRepository|null
     */
    public function getRemoteRepository(): ?AbstractRemoteRepository
    {
        return $this->remoteRepository;
    }

    /**
     * Returns the service objects.
     *
     * @param string $name Service name
     *
     * @return object
     */
    public function getService(string $name): object
    {
        // Services created?
        if (isset($this->serviceInstances[$name])) {
            return $this->serviceInstances[$name];
        }

        if (!isset($this->services[$name])) {
            throw new InvalidArgumentException(sprintf('Azirax service "%s" not found.', $name));
        }

        $service = $this->services[$name];

        // Bind Azirax to the Closure
        if ($service instanceof Closure) {
            $this->serviceInstances[$name] = $service();
        } else {
            $this->serviceInstances[$name] = $service;
        };

        return $this->serviceInstances[$name];
    }

    /**
     * Returns the azirax version.
     *
     * @return string
     */
    public static function getVersion(): string
    {
        return self::VERSION_MAJOR . '.' . self::VERSION_MINOR . '.' . self::VERSION_PATCH . (self::VERSION_DEV ? '-dev' : '');
    }

    /**
     * Set the current version.
     *
     * @param string $version   Version as string
     *
     * @return void
     */
    public function setVersion(string $version): void
    {
        $this->config['version'] = $version;
    }

    /**
     * Azirax is development mode?
     *
     * @return bool
     */
    public static function isDev(): bool
    {
        return self::VERSION_DEV;
    }

    /**
     * Returns the service provider classes.
     *
     * @return array
     */
    protected function getServiceProviders(): array
    {
        return [
            CodeParserProvider::class,
            DocBlockParserProvider::class,
            FilterProvider::class,
            IndexerProvider::class,
            ParserProvider::class,
            ParserContextProvider::class,
            PhpParserProvider::class,
            PhpTraverserProvider::class,
            PrettyPrinterProvider::class,
            ProjectProvider::class,
            RendererProvider::class,
            StoreProvider::class,
            ThemesProvider::class,
            TraverserProvider::class,
            TreeProvider::class,
            TwigProvider::class,
            VersionsProvider::class,
        ];
    }
}
