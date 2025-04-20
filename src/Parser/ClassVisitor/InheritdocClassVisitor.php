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

namespace Azirax\Documentation\Parser\ClassVisitor;

use Azirax\Documentation\Parser\ClassVisitorInterface;
use Azirax\Documentation\Reflection\Interfaces\ClassReflectionInterface;

use function strtolower;
use function trim;

/**
 * Class visitor for inheritdoc's.
 *
 * @package      Azirax\Documentation\Parser\ClassVisitor
 * @author       Rene Dziuba <php.tux@web.de>
 * @author       Fabien Potencier <fabien@symfony.com>
 * @copyright    Copyright (c) 2025 The Authors
 * @license      <https://opensource.org/license/mit> MIT License
 */
class InheritdocClassVisitor implements ClassVisitorInterface
{
    /**
     * Visit the class reflection object and make changes.
     *
     * @param ClassReflectionInterface $class Class reflection object
     *
     * @return bool Class data was modified or not.
     */
    public function visit(ClassReflectionInterface $class): bool
    {
        $modified = false;

        foreach ($class->getMethods() as $name => $method) {
            if (!$parentMethod = $class->getParentMethod($name)) {
                continue;
            }

            foreach ($method->getParameters() as $paramName => $parameter) {
                if (!$parentParameter = $parentMethod->getParameter($paramName)) {
                    continue;
                }

                if ($parameter->getShortDesc() != $parentParameter->getShortDesc()) {
                    $parameter->setShortDesc($parentParameter->getShortDesc());
                    $modified = true;
                }

                if ($parameter->getHint() != $parentParameter->getRawHint()) {
                    // FIXME: should test for a raw hint from tags, not the one from PHP itself
                    $parameter->setHint($parentParameter->getRawHint());
                    $modified = true;
                }
            }

            if ($method->getHint() != $parentMethod->getRawHint()) {
                $method->setHint($parentMethod->getRawHint());
                $modified = true;
            }

            if ($method->getHintDesc() != $parentMethod->getHintDesc()) {
                $method->setHintDesc($parentMethod->getHintDesc());
                $modified = true;
            }

            $shortDesc = $method->getShortDesc() ?? '';
            if ('{@inheritdoc}' === strtolower(trim($shortDesc)) || !$method->getDocComment()) {
                if ($shortDesc != $parentMethod->getShortDesc()) {
                    $method->setShortDesc($parentMethod->getShortDesc());
                    $modified = true;
                }

                if ($method->getLongDesc() != $parentMethod->getLongDesc()) {
                    $method->setLongDesc($parentMethod->getLongDesc());
                    $modified = true;
                }

                if ($method->getExceptions() != $parentMethod->getRawExceptions()) {
                    $method->setExceptions($parentMethod->getRawExceptions());
                    $modified = true;
                }
            }
        }

        return $modified;
    }

}
