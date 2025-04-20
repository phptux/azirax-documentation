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
use Azirax\Documentation\Parser\ParserContext;
use Azirax\Documentation\Reflection\Interfaces\ClassReflectionInterface;
use Azirax\Documentation\Reflection\PropertyReflection;

use function count;

/**
 * Class visitor for class properties.
 *
 * Looks for `@property` tags on classes in the format of:.
 *
 * `@property [<type>] [name] [<description>]`
 *
 * Or -read -write properties
 *
 * `@property-read [Type] [name] [<description>]`
 * `@property-write [Type] [name] [<description>]`
 *
 * @package      Azirax\Documentation\Parser\ClassVisitor
 * @author       Rene Dziuba <php.tux@web.de>
 * @author       Fabien Potencier <fabien@symfony.com>
 * @copyright    Copyright (c) 2025 The Authors
 * @license      <https://opensource.org/license/mit> MIT License
 */
class PropertyClassVisitor implements ClassVisitorInterface
{
    /**
     * Parser context object
     *
     * @var ParserContext
     */
    protected ParserContext $context;

    /**
     * Constructor for PropertyClassVisitor
     *
     * @param ParserContext $context Parser context object
     */
    public function __construct(ParserContext $context)
    {
        $this->context = $context;
    }

    /**
     * Visit the class reflection object and make changes.
     *
     * @param ClassReflectionInterface $class Class reflection object
     *
     * @return bool Class data was modified or not.
     */
    public function visit(ClassReflectionInterface $class): bool
    {
        $modified        = false;
        $properties      = $class->getTags('property');
        $propertiesRead  = $class->getTags('property-read');
        $propertiesWrite = $class->getTags('property-write');

        if (!empty($properties)) {
            foreach ($properties as $propertyTag) {
                if ($this->injectProperty($class, $propertyTag, 'property')) {
                    $modified = true;
                }
            }
        }

        if (!empty($propertiesRead)) {
            foreach ($propertiesRead as $propertyTag) {
                if ($this->injectProperty($class, $propertyTag, 'property-read')) {
                    $modified = true;
                }
            }
        }

        if (!empty($propertiesWrite)) {
            foreach ($propertiesWrite as $propertyTag) {
                if ($this->injectProperty($class, $propertyTag, 'property-write')) {
                    $modified = true;
                }
            }
        }
        return $modified;
    }

    /**
     * Adds a new property to the class using an array of tokens.
     *
     * @param ClassReflectionInterface $class       Class reflection
     * @param array                    $propertyTag Property tag contents
     * @param string                   $tagName     The tag name, for example: property-read
     */
    protected function injectProperty(ClassReflectionInterface $class, array $propertyTag, string $tagName): bool
    {
        if (count($propertyTag) === 3 && !empty($propertyTag[1])) {
            $property = new PropertyReflection($propertyTag[1], $class->getLine());
            $property->setDocComment($propertyTag[2]);
            $property->setShortDesc($propertyTag[2]);

            if (!empty($propertyTag[0])) {
                $property->setHint($propertyTag[0]);
            }

            if ($tagName === 'property-read') {
                $property->setReadOnly(true);
            } elseif ($tagName === 'property-write') {
                $property->setWriteOnly(true);
            }

            $class->addProperty($property);


            return true;
        }

        return false;
    }
}
