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

namespace Azirax\Documentation\Version;

use LogicException;

use function is_array;

/**
 * Single version collection.
 *
 * @package      Azirax\Documentation\Version
 * @author       Rene Dziuba <php.tux@web.de>
 * @author       Fabien Potencier <fabien@symfony.com>
 * @copyright    Copyright (c) 2025 The Authors
 * @license      <https://opensource.org/license/mit> MIT License
 */
class SingleVersionCollection extends VersionCollection
{
    /**
     * Add the Version(s).
     *
     * @param array|string|Version $version  Version(s)
     * @param string|null          $longName Version long name
     *
     * @return $this
     */
    public function add(array|string|Version $version, ?string $longName = null): static
    {
        if (count($this->versions) > 0) {
            throw new LogicException('A SingleVersionCollection can only contain one Version');
        }

        parent::add($version, $longName);

        return $this;
    }

    /**
     * Switch to version.
     *
     * @param Version $version Version object
     *
     * @return void
     */
    protected function switchVersion(Version $version): void
    {
    }

}
