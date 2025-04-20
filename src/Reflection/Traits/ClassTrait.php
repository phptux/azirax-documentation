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

use Azirax\Documentation\Reflection\ClassReflection;

/**
 * Trait for inject the class reflection class object.
 *
 * @package      Azirax\Documentation\Reflection
 * @author       Rene Dziuba <php.tux@web.de>
 * @author       Fabien Potencier <fabien@symfony.com>
 * @copyright    Copyright (c) 2025 The Authors
 * @license      <https://opensource.org/license/mit> MIT License
 */
trait ClassTrait
{
    /**
     * Class reflection object.
     *
     * @var ClassReflection|null
     */
    private ?ClassReflection $classReflection = null;

    /**
     * Returns the class reflection object.
     *
     * @return ClassReflection|null
     */
    public function getClass(): ?ClassReflection
    {
        return $this->classReflection;
    }

    /**
     * Set the class reflection object.
     *
     * @param ClassReflection $classReflection Class reflection object
     *
     * @return void
     */
    public function setClass(ClassReflection $classReflection): void
    {
        $this->classReflection = $classReflection;
    }
}
