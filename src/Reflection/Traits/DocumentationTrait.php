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

namespace Azirax\Documentation\Reflection\Traits;

/**
 * Trait for documentations.
 *
 * @package      Azirax\Documentation\Reflection\Traits
 * @author       Rene Dziuba <php.tux@web.de>
 * @author       Fabien Potencier <fabien@symfony.com>
 * @copyright    Copyright (c) 2025 The Authors
 * @license      <https://opensource.org/license/mit> MIT License
 */
trait DocumentationTrait
{
    /**
     * Doc comment
     *
     * @var string|null
     */
    protected ?string $docComment = null;

    /**
     * Long description
     *
     * @var string|null
     */
    protected ?string $longDesc = null;

    /**
     * Short description
     *
     * @var string|null
     */
    protected ?string $shortDesc = null;

    /**
     * Returns the raw comment.
     *
     * @return string|null
     */
    public function getDocComment(): ?string
    {
        return $this->docComment;
    }

    /**
     * Returns the long description.
     *
     * @return string|null
     */
    public function getLongDesc(): ?string
    {
        return $this->longDesc;
    }

    /**
     * Returns the short description.
     *
     * @return string|null
     */
    public function getShortDesc(): ?string
    {
        return $this->shortDesc;
    }

    /**
     * Set the raw comment.
     *
     * @param string|null $comment Raw comment
     */
    public function setDocComment(?string $comment): void
    {
        $this->docComment = $comment;
    }

    /**
     * Set the long description.
     *
     * @param string|null $longDesc Description text
     */
    public function setLongDesc(?string $longDesc): void
    {
        $this->longDesc = $longDesc;
    }

    /**
     * Set the short description.
     *
     * @param string|null $shortDesc Description text
     */
    public function setShortDesc(?string $shortDesc): void
    {
        $this->shortDesc = $shortDesc;
    }
}
