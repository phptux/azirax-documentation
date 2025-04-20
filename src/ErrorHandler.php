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

use ErrorException;

use function error_reporting;
use function set_error_handler;
use function sprintf;

/**
 * Error handler class.
 *
 * @package      Azirax\Documentation
 * @author       Rene Dziuba <php.tux@web.de>
 * @author       Fabien Potencier <fabien@symfony.com>
 * @copyright    Copyright (c) 2025 The Authors
 * @license      <https://opensource.org/license/mit> MIT License
 */
final class ErrorHandler
{
    /**
     * Human error levels
     */
    private const array ERROR_LEVELS
        = [
            E_ERROR             => 'Error',
            E_WARNING           => 'Warning',
            E_NOTICE            => 'Notice',
            E_USER_ERROR        => 'User Error',
            E_USER_WARNING      => 'User Warning',
            E_USER_NOTICE       => 'User Notice',
            E_USER_DEPRECATED   => 'User Deprecation notice',
            E_RECOVERABLE_ERROR => 'Catchable Fatal Error',
        ];

    /**
     * Registers the error handler.
     */
    public static function register(): void
    {
        set_error_handler([new ErrorHandler(), 'handle']);
    }

    /**
     * Handle the error.
     *
     * @param int    $level   Error level
     * @param string $message Error message
     * @param string $file    Error filename
     * @param int    $line    Error line umber
     *
     * @return bool
     * @throws ErrorException When error_reporting returns error
     */
    public function handle(int $level, string $message, string $file = 'unknown', int $line = 0): bool
    {
        /**
         * Check if Error Control Operator (@) was used
         */
        $isSilenced = !(error_reporting() & $level);

        if (!$isSilenced) {
            throw new ErrorException(sprintf('%s: %s in %s line %d', self::ERROR_LEVELS[$level] ?? $level, $message, $file, $line));
        }

        return false;
    }
}
