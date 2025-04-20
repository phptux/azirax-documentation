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

namespace Azirax\Documentation\Reflection\Interfaces;

/**
 * Interface for documentations.
 *
 * @package      Azirax\Documentation\Reflection\Interfaces
 * @author       Rene Dziuba <php.tux@web.de>
 * @author       Fabien Potencier <fabien@symfony.com>
 * @copyright    Copyright (c) 2025 The Authors
 * @license      <https://opensource.org/license/mit> MIT License
 */
interface DocumentationInterface
{
    /**
     * Returns the raw comment.
     *
     * @return string|null
     */
    public function getDocComment(): ?string;

    /**
     * Returns the long description.
     *
     * @return string|null
     */
    public function getLongDesc(): ?string;

    /**
     * Returns the short description.
     *
     * @return string|null
     */
    public function getShortDesc(): ?string;

    /**
     * Set the raw comment.
     *
     * @param string|null $comment Raw comment
     */
    public function setDocComment(?string $comment): void;

    /**
     * Set the long description.
     *
     * @param string|null $longDesc Description text
     */
    public function setLongDesc(?string $longDesc): void;

    /**
     * Set the short description.
     *
     * @param string|null $shortDesc Description text
     */
    public function setShortDesc(?string $shortDesc): void;
}
