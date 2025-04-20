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

/**
 * Message for the callbacks.
 *
 * @package      Azirax\Documentation
 * @author       Rene Dziuba <php.tux@web.de>
 * @author       Fabien Potencier <fabien@symfony.com>
 * @copyright    Copyright (c) 2025 The Authors
 * @license      <https://opensource.org/license/mit> MIT License
 */
class Message
{
    public const int PARSE_ERROR             = 1;
    public const int PARSE_CLASS             = 2;
    public const int PARSE_VERSION_FINISHED  = 3;
    public const int RENDER_PROGRESS         = 4;
    public const int RENDER_VERSION_FINISHED = 5;
    public const int SWITCH_VERSION          = 6;
}
