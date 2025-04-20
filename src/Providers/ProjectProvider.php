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

namespace Azirax\Documentation\Providers;

use Azirax\Documentation\Azirax;
use Azirax\Documentation\Project;

/**
 * Service provider register the service `project`.
 *
 * @package      Azirax\Documentation\Providers
 * @author       Rene Dziuba <php.tux@web.de>
 * @author       Fabien Potencier <fabien@symfony.com>
 * @copyright    Copyright (c) 2025 The Authors
 * @license      <https://opensource.org/license/mit> MIT License
 */
class ProjectProvider implements ServiceProviderInterface
{
    /**
     * Register the service.
     *
     * @param Azirax $azirax Azirax object
     *
     * @return void
     */
    public function register(Azirax $azirax): void
    {
        $azirax->addService(
            'project',
            function () use ($azirax) {
                $project = new Project(
                    $azirax->getService('store'),
                    $azirax->getService('versions'),
                    [
                        'build_dir'             => $azirax->getConfig('buildDir'),
                        'cache_dir'             => $azirax->getConfig('cacheDir'),
                        'theme'                 => $azirax->getConfig('theme'),
                        'title'                 => $azirax->getConfig('title'),
                        'insert_todos'          => $azirax->getConfig('todos'),
                        'favicon'               => $azirax->getConfig('favicon'),
                        'include_parent_data'   => $azirax->getConfig('includeParentData'),
                        'remote_repository'     => $azirax->getRemoteRepository(),
                        'default_opened_level'  => $azirax->getConfig('defaultOpenedLevel'),
                        'source_url'            => $azirax->getConfig('sourceUrl'),
                        'base_url'              => $azirax->getConfig('baseUrl'),
                        'footer_link'           => $azirax->getConfig('footerLink'),
                        'sort_class_properties' => $azirax->getConfig('sortProperties'),
                        'sort_class_methods'    => $azirax->getConfig('sortMethods'),
                        'sort_class_constants'  => $azirax->getConfig('sortConstants'),
                        'sort_class_traits'     => $azirax->getConfig('sortTraits'),
                        'sort_class_interfaces' => $azirax->getConfig('sortInterfaces'),
                        'sort_class_enums'      => $azirax->getConfig('sortEnums'),
                        'language'              => $azirax->getConfig('language'),
                    ],
                );
                $project->setParser($azirax->getService('parser'));
                $project->setRenderer($azirax->getService('renderer'));

                return $project;
            },
        );
    }

}
