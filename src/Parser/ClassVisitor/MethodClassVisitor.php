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
use Azirax\Documentation\Reflection\Interfaces\ModifierInterface;
use Azirax\Documentation\Reflection\MethodReflection;
use Azirax\Documentation\Reflection\ParameterReflection;

use function count;
use function explode;
use function implode;
use function preg_match;
use function str_starts_with;
use function substr;
use function trim;

/**
 * Class visitor for class methods.
 *
 * Looks for `@method` tags on classes in the format of:
 *
 * `@method` [[static] return type] [name]([[type] [parameter]<, ...>]) [<description>]
 *
 * @package      Azirax\Documentation\Parser\ClassVisitor
 * @author       Rene Dziuba <php.tux@web.de>
 * @author       Fabien Potencier <fabien@symfony.com>
 * @copyright    Copyright (c) 2025 The Authors
 * @license      <https://opensource.org/license/mit> MIT License
 */
class MethodClassVisitor implements ClassVisitorInterface
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

        $methods = $class->getTags('method');
        if (!empty($methods)) {
            foreach ($methods as $methodTag) {
                if ($this->injectMethod($class, implode(' ', $methodTag))) {
                    $modified = true;
                }
            }
        }

        return $modified;
    }

    /**
     * Adds a new method to the class using an array of tokens.
     *
     * @param ClassReflectionInterface $class     Class reflection
     * @param string          $methodTag Method tag contents
     *
     * @return bool
     */
    protected function injectMethod(ClassReflectionInterface $class, string $methodTag): bool
    {
        $data = $this->parseMethod($methodTag);

        // Bail if the method format is invalid
        if ($data === null) {
            return false;
        }

        $method = new MethodReflection($data['name'], $class->getLine());
        $method->setDocComment($data['description']);
        $method->setShortDesc($data['description']);

        if ($data['hint']) {
            $method->setHint([[$data['hint'], null]]);
        }
        if ($data['isStatic']) {
            $method->setModifier(ModifierInterface::STATIC);
        }

        // Add arguments to the method
        foreach ($data['args'] as $name => $arg) {
            $param = new ParameterReflection($name, $class->getLine());
            if (!empty($arg['hint'])) {
                $param->setHint([[$arg['hint'], null]]);
            }
            if (!empty($arg['default'])) {
                $param->setDefault($arg['default']);
            }
            $param->setVariadic($arg['isVariadic']);
            $method->addParameter($param);
        }

        $class->addMethod($method);

        return true;
    }

    /**
     * Parse the parts of an `@method` tag into an associative array.
     *
     * Original `@method` parsing by https://github.com/phpDocumentor/ReflectionDocBlock/blob/5.2.0/src/DocBlock/Tags/Method.php#L84
     *
     * @param string $body Method tag contents
     *
     * @return array|null
     *
     * @license   MIT
     * @copyright 2010 Mike van Riel
     */
    protected function parseMethod(string $body): ?array
    {
        // 1. none or more whitespace
        // 2. optionally the keyword "static" followed by whitespace
        // 3. optionally a word with underscores followed by whitespace : as
        //    type for the return value
        // 4. then optionally a word with underscores followed by () and
        //    whitespace : as method name as used by phpDocumentor
        // 5. then a word with underscores, followed by ( and any character
        //    until a ) and whitespace : as method name with signature
        // 6. any remaining text : as description
        $regex = '/^
            # Static keyword
            # Declares a static method ONLY if type is also present
            (?:
                (static)
                \s+
            )?
            # Return type
            (?:
                (
                    (?:[\w\|_\\\\]*\$this[\w\|_\\\\]*)
                    |
                    (?:
                        (?:[\w\|_\\\\]+)
                        # array notation
                        (?:\[\])*
                    )*+
                )
                \s+
            )?
            # Method name
            ([\w_]+)
            # Arguments
            (?:
                \((.*(?=\)))\)
            )?
            \s*
            # Description
            (.*)
        $/sux';
        if (!preg_match($regex, $body, $matches)) {
            return null;
        }

        [, $static, $returnType, $methodName, $argumentLines, $description] = $matches;

        $isStatic = $static === 'static';

        if ($returnType === '') {
            $returnType = 'void';
        }

        $arguments = [];
        if ($argumentLines !== '') {
            $argumentsExploded = explode(',', $argumentLines);
            foreach ($argumentsExploded as $argument) {
                $argument           = explode(' ', trim($argument), 2);
                $defaultValue       = '';
                $argumentType       = '';
                $argumentName       = '';
                $hasVariadicAtStart = str_starts_with($argument[0], '...$');
                if (str_starts_with($argument[0], '$') || $hasVariadicAtStart) {      // Only param name, example: $param1
                    $argumentName = substr($argument[0], $hasVariadicAtStart ? 4 : 1);// Remove $
                } else {// Type and param name, example: string $param1 or just a type, example: string
                    $argumentType = $argument[0];
                    if (isset($argument[1])) {// Type and param name
                        $hasVariadicAtStart = str_starts_with($argument[1], '...$');
                        $argumentName       = substr($argument[1], $hasVariadicAtStart ? 4 : 1);// Remove $
                        $defaultPart        = explode('=', $argumentName, 2);
                        if (count($defaultPart) === 2) {// detected varName = defaultValue
                            $argumentName = $defaultPart[0];
                            $defaultValue = $defaultPart[1];
                        }
                    }
                }

                $argumentName = trim($argumentName);
                $argumentType = trim($argumentType);
                $defaultValue = trim($defaultValue);

                $arguments[$argumentName] = [
                    'isVariadic' => $hasVariadicAtStart,
                    'name' => $argumentName,
                    'hint' => $argumentType,
                    'default' => $defaultValue,
                ];
            }
        }

        return [
            'isStatic' => $isStatic,
            'hint' => trim($returnType),
            'name' => $methodName,
            'args' => $arguments,
            'description' => $description,
        ];
    }
}
