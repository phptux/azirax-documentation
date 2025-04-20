# Azirax documentation, a PHP API documentation generator. Fork of Sami.

## Installation

> Caution!
> Azirax requires **PHP 8.3** or later.


## Configuration vars

This configuration variables:

| Variable name        | Type                                                           | Description                                                                                                                                                                           |
|----------------------|----------------------------------------------------------------|---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------|
| `debug`              | boolean                                                        | If `TRUE`, the script not store the cache data                                                                                                                                        |
| `buildDir`           | string                                                         | Directory path for save the generated API                                                                                                                                             |
| `cacheDir`           | string                                                         | Directory path for the store data                                                                                                                                                     |
| `sourceDir`          | string                                                         | Directory path to the script `ariarx-documentation`                                                                                                                                   |
| `parseFilter`        | int                                                            | 0 = All classes, methods and all properties<br/>*1 = All classes, not private methods and not private properties*<br/>2 = All classes, only public methods and only public properties |
| `language`           | string                                                         | Language for translation (default `en`)                                                                                                                                               |
| `title`              | string                                                         | Website title                                                                                                                                                                         |
| `theme`              | string                                                         | Theme name (default `default`)                                                                                                                                                        |
| `sortConstants`      | bool                                                           | Sort class constants (default `false`)                                                                                                                                                |
| `sortEnums`          | bool                                                           | Sort enums (default `false`)                                                                                                                                                          |
| `sortInterfaces`     | bool                                                           | Sort interfaces (default `false`)                                                                                                                                                     |
| `sortMethods`        | bool                                                           | Sort class methods (default `false`)                                                                                                                                                  |
| `sortProperties`     | bool                                                           | Sort class properties (default `false`)                                                                                                                                               |
| `sortTraits`         | bool                                                           | Sort traits (default `false`)                                                                                                                                                         |
| `todos`              | bool                                                           | Insert todos (default `false`)                                                                                                                                                        |
| `favicon`            | string or null                                                 | Name of the favicon (default `null`)                                                                                                                                                  |
| `includeParentData`  | bool                                                           | Include parent properties and methods on class pages                                                                                                                                  |
| `remoteRepository`   | Azirax\Documentation\RemoteRepository\AbstractRemoteRepository | Remote repository object                                                                                                                                                              |
| `defaultOpenedLevel` | int                                                            | Open level for the tree (default `2`)                                                                                                                                                 |
| `sourceUrl`          | string                                                         | URL to the source, Necessary to enable the `opensearch.xml` file generation.                                                                                                          |
| `baseUrl`            | string or null                                                 | Url to the API generated files.                                                                                                                                                       |
| `footerLink`         | array                                                          | Array with link data.                                                                                                                                                                 |
| `templateDirs`       | array                                                          | Array with custom theme directories                                                                                                                                                   |
| `phalconVersion`     | string                                                         | Phalcon Framework version number. We needed for the API url to Phalcon site.                                                                                                          |

```php
$footerLink = [
    'href'        => 'https://github.com/phptux/azorax-documentation',
    'rel'         => 'noreferrer noopener',
    'target'      => '_blank',
    'before_text' => 'You can edit the configuration',
    'link_text'   => 'on this', // Required if the href key is set
    'after_text'  => 'repository',
]
```

## Configuration

Before generating documentation, you must create a configuration file.
Here is the simplest possible one:

```php
<?php

return new Azirax\Documentation\Azirax('/path/to/yourlib/src');
```

The configuration file must return an instance of ``Azirax\Documentation\Azirax`` and the first
argument of the constructor is the path to the code you want to generate
documentation for.

Actually, instead of a directory, you can use any valid PHP iterator (and for
that matter any instance of the [Symfony Finder](https://symfony.com/doc/current/components/finder.html) class):

```php
<?php

use Azirax\Documentation\Azirax;
use Symfony\Component\Finder\Finder;

$iterator = Finder::create()
    ->files()
    ->name('*.php')
    ->exclude('Resources')
    ->exclude('Tests')
    ->in('/path/to/yourlib/src');

return new Azirax($iterator);
```

The ``Azirax`` constructor optionally takes an array of options as a second
argument:

```php
return new Azirax($iterator, [
    'title'                => 'yourlib API',
    'language'             => 'en', // Could be 'de'
    'buildDir'            => __DIR__ . '/build',
    'cacheDir'            => __DIR__ . '/cache',
    'sourceDir'           => '/path/to/repository/',
    'remoteRepository'    => new GitHubRemoteRepository('username/repository', '/path/to/repository'),
    'defaultOpenedLevel' => 2, // optional, 2 is the default value
]);
```

And here is an example of how you can configure different versions:

```php
<?php

use Azirax\Documentation\Azirax;
use Azirax\Documentation\Version\GitVersionCollection;
use Azirax\Documentation\RemoteRepository\GitHubRemoteRepository;
use Symfony\Component\Finder\Finder;

// Directory to local Symfony console
// Generate with "git clone https://github.com/symfony/console.git"
$dir = dirname(__DIR__) . '/console';

// Iterator to the repo files
$iterator = Finder::create()
                  ->files()
                  ->name('*.php')
                  ->exclude('Tester')
                  ->exclude('Tests')
                  ->in($dir);

// generate documentation for all v6.0.* tags, the 6.0 branch, and the main one (7.2)
$versions = GitVersionCollection::create($dir)
    // In a non case-sensitive way, tags containing "PR", "RC", "BETA" and "ALPHA" will be filtered out
    // To change this, use: `$versions->setFilter(static function (string $version): bool { // ... });`
    ->addFromTags('v6.0.*')
    ->add('6.0', '6.0 branch')
    ->add('7.2', 'main branch');

// Returns the configuration
return new Azirax($iterator, [
    'version'            => $versions,
    'title'              => 'Console',
    'language'           => 'en', // Could be 'fr'
    'theme'              => 'dark',
    'buildDir'           => dirname(__DIR__) . '/build/%version%',
    'cacheDir'           => dirname(__DIR__) . '/cache/%version%',
    'sourceDir'          => $dir,
    'remoteRepository'   => new GitHubRemoteRepository('symfony/console', $dir),
    'defaultOpenedLevel' => 2, // optional, 2 is the default value
]);
```

To enable `OpenSearch <https://en.wikipedia.org/wiki/OpenSearch>`_ feature in your users browsers:

```php
<?php

use Azirax\Documentation\Azirax;
use Symfony\Component\Finder\Finder;

$dir = '/path/to/yourlib/src';
$iterator = Finder::create()
    ->files()
    ->name('*.php')
    ->exclude('Resources')
    ->exclude('Tests')
    ->in($dir);

return new Azirax($iterator, [
    'title'    => 'Project Api Documentation',
    // Necessary to enable the opensearch.xml file generation
    'base_url' => 'https://apidocs.company.tld/',
    // ... more configs
]);
```

## Rendering

Now that we have a configuration file, let's generate the API documentation:

```bash
$ ./bin/azdoc update /path/to/config.php
```

The generated documentation can be found under the configured ``build/``
directory (note that the client side search engine does not work on Chrome due
to JavaScript execution restriction, unless Chrome is started with the
"--allow-file-access-from-files" option -- it works fine in Firefox).

By default, Azirax is configured to run in "incremental" mode. It means that when
running the ``update`` command, Azirax only re-generates the files that needs to
be updated based on what has changed in your code since the last execution.

Azirax also detects problems in your phpdoc and can tell you what you need to fix
if you add the ``-v`` option:

```bash
$ ./bin/azdoc update /path/to/config.php -v
```

## Creating a Theme

If the default themes do not suit your needs, you can very easily create a new
one, or just override an existing one.

A theme is just a directory with a ``manifest.yml`` file that describes the
theme (this is a YAML file):

```yaml
name:   markdown-custom
parent: default
```

The above configuration creates a new ``markdown-custom`` theme based on the
``default`` built-in theme. To override a template, just create a file with
the same name as the original one. For instance, here is how you can extend the
default class template to prefix the class name with "Class " in the class page
title:

```twig
{# pages/class.twig #}

{% extends 'default/pages/class.twig' %}

{% block title %}Class {{ parent() }}{% endblock %}
```

If you are familiar with Twig, you will be able to very easily tweak every
aspect of the templates as everything has been well isolated in named Twig
blocks.

A theme can also add more templates and static files. 
Here is the manifest for the default theme:

```yaml
name: default

static:
  'css/theme.min.css': 'css/theme.min.css'
  'js/jquery-3.7.1.min.js': 'js/jquery-3.7.1.min.js'
  'js/uikit.min.js': 'js/uikit.min.js'
  'js/uikit-icons-material.min.js': 'js/uikit-icons-material.min.js'
  'js/autocomplete.min.js': 'js/autocomplete.min.js'
  'js/highlight.min.js': 'js/highlight.min.js'
  'img/favicon/favicon.ico': 'img/favicon/favicon.ico'
  'img/favicon/apple-touch-icon.png': 'img/favicon/apple-touch-icon.png'
  'img/favicon/favicon-32x32.png': 'img/favicon/favicon-32x32.png'
  'img/favicon/favicon-16x16.png': 'img/favicon/favicon-16x16.png'
  'img/favicon/android-chrome-192x192.png': 'img/favicon/android-chrome-192x192.png'
  'img/favicon/android-chrome-512x512.png': 'img/favicon/android-chrome-512x512.png'
  'img/favicon/site.webmanifest': 'img/favicon/site.webmanifest'
  'img/logo.png': 'img/logo.png'

global:
  'index.twig':      'index.html'
  'doc-index.twig':  'doc-index.html'
  'namespaces.twig': 'namespaces.html'
  'classes.twig':    'classes.html'
  'interfaces.twig': 'interfaces.html'
  'traits.twig':     'traits.html'
  'enums.twig':      'enums.html'
  'opensearch.twig': 'opensearch.xml'
  'search.twig':     'search.html'
  'azirax.js.twig':  'azirax.js'
  'azirax-search.json.twig':  'azirax-search.json'

namespace:
  'namespace.twig': '%s.html'

class:
  'class.twig': '%s.html'
```

Files are contained into sections, depending on how Azirax needs to treat them:

* ``static``: Files are copied as is (for assets like images, stylesheets, or JavaScript files);
* ``global``: Templates that do not depend on the current class context;
* ``namespace``: Templates that should be generated for every namespace;
* ``class``: Templates that should be generated for every class.

## Theme configuration

```php
<?php

return new Azirax($iterator, [
    // [...]
    'theme'         => 'my-theme-name',
    // Add the path to the theme/themes
    'template_dirs' => [__DIR__ . '/themes/my-theme-name'],
    // [...]
    ]
);
```

