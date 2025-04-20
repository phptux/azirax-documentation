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
use Azirax\Documentation\Parser\ClassVisitor\InheritdocClassVisitor;
use Azirax\Documentation\Parser\ClassVisitor\MethodClassVisitor;
use Azirax\Documentation\Parser\ClassVisitor\PropertyClassVisitor;
use Azirax\Documentation\Parser\ClassVisitor\ViewSourceClassVisitor;
use Azirax\Documentation\Parser\FunctionVisitor\ViewSourceFunctionVisitor;
use Azirax\Documentation\Parser\ProjectTraverser;

/**
 * Service provider register the service `traverser`.
 *
 * @package      Azirax\Documentation\Providers
 * @author       Rene Dziuba <php.tux@web.de>
 * @author       Fabien Potencier <fabien@symfony.com>
 * @copyright    Copyright (c) 2025 The Authors
 * @license      <https://opensource.org/license/mit> MIT License
 */
class TraverserProvider implements ServiceProviderInterface
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
            'traverser',
            function () use ($azirax) {
                $visitors = [
                    new InheritdocClassVisitor(),
                    new MethodClassVisitor(),
                    new PropertyClassVisitor($azirax->getService('parserContext')),
                ];

                $remoteRepository = $azirax->getRemoteRepository();
                if ($remoteRepository !== null) {
                    $visitors[] = new ViewSourceClassVisitor($remoteRepository);
                    $visitors[] = new ViewSourceFunctionVisitor($remoteRepository);
                }

                return new ProjectTraverser($visitors);
            }
        );
    }

}
