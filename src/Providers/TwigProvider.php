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

use Azirax\Documentation\Renderer\TwigExtension;
use Twig\Environment;
use Twig\Extension\DebugExtension;
use Twig\Loader\FilesystemLoader;
use Wdes\phpI18nL10n\Launcher;
use Wdes\phpI18nL10n\plugins\MoReader;

use Wdes\phpI18nL10n\Twig\Extension\I18n;

use function dirname;

/**
 * Service provider register the service `twig`.
 *
 * @package      Azirax\Documentation\Providers
 * @author       Rene Dziuba <php.tux@web.de>
 * @author       Fabien Potencier <fabien@symfony.com>
 * @copyright    Copyright (c) 2025 The Authors
 * @license      <https://opensource.org/license/mit> MIT License
 */
class TwigProvider implements ServiceProviderInterface
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
            'twig',
            function () use ($azirax) {
                $dataDir  = dirname(__DIR__, 2) . '/locale/';
                $moReader = new MoReader(
                    ['localeDir' => $dataDir]
                );
                $moReader->readFile($dataDir . $azirax->getConfig('language') . '.mo');
                Launcher::setPlugin($moReader);

                $twig = new Environment(
                    new FilesystemLoader(['/']),
                    [
                        'strict_variables' => true,
                        'debug' => true,
                        'auto_reload' => true,
                        'cache' => false,
                    ]
                );

                $twig->addExtension(new TwigExtension());
                $twig->addExtension(new I18n());
                $twig->addExtension(new DebugExtension());

                return $twig;
            }
        );
    }

}
