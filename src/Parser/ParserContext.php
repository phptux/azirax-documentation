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

namespace Azirax\Documentation\Parser;

use Azirax\Documentation\Parser\Filters\FilterInterface;
use Azirax\Documentation\Reflection\Interfaces\ClassReflectionInterface;
use Azirax\Documentation\Reflection\Interfaces\FunctionReflectionInterface;
use PhpParser\PrettyPrinter;

/**
 * Parser context class.
 *
 * @package      Azirax\Documentation\Parser
 * @author       Rene Dziuba <php.tux@web.de>
 * @author       Fabien Potencier <fabien@symfony.com>
 * @copyright    Copyright (c) 2025 The Authors
 * @license      <https://opensource.org/license/mit> MIT License
 */
class ParserContext
{
    /** Array with aliases
     *
     * @var array
     */
    protected array $aliases = [];

    /**
     * Class reflection object
     *
     * @var ClassReflectionInterface|null
     */
    protected ?ClassReflectionInterface $class = null;

    /**
     * Classes
     *
     * @var array
     */
    protected array $classes = [];

    /**
     * Doc-block parser object
     *
     * @var DocBlockParser
     */
    protected DocBlockParser $docBlockParser;

    /**
     * Array with errors
     *
     * @var ParserError[]
     */
    protected array $errors = [];

    /**
     * Filename
     *
     * @var string|null
     */
    protected ?string $file = null;

    /**
     * Filter object
     *
     * @var FilterInterface
     */
    protected FilterInterface $filter;

    /**
     * Array with functions
     *
     * @var FunctionReflectionInterface[]
     */
    protected array $functions = [];

    /**
     * Hash
     *
     * @var string|null
     */
    protected ?string $hash = null;

    /**
     * Namespace name
     *
     * @var string|null
     */
    protected ?string $namespace = null;

    /**
     * Pretty printer object
     *
     * @var PrettyPrinter
     */
    protected PrettyPrinter $prettyPrinter;

    /**
     * Constructor for ParserContext
     *
     * @param FilterInterface $filter         Filter object
     * @param DocBlockParser  $docBlockParser DocBlock parser object
     * @param PrettyPrinter   $prettyPrinter  Pretty printer object
     */
    public function __construct(FilterInterface $filter, DocBlockParser $docBlockParser, PrettyPrinter $prettyPrinter)
    {
        $this->filter         = $filter;
        $this->docBlockParser = $docBlockParser;
        $this->prettyPrinter  = $prettyPrinter;
        $this->functions      = [];
    }

    /**
     * Add an alias.
     *
     * @param string|null $alias Alias
     * @param string      $name  Alias name
     *
     * @return void
     */
    public function addAlias(?string $alias, string $name): void
    {
        $this->aliases[$alias] = $name;
    }

    /**
     * Add an error.
     *
     * @param string|null $name  Error name
     * @param int         $line  Error line number
     * @param string      $error Error message
     *
     * @return void
     */
    public function addError(?string $name, int $line, string $error): void
    {
        $this->errors[] = new ParserError(
            sprintf('%s on "%s"', $error, $name),
            $this->file,
            $line,
        );
    }

    /**
     * Add an array with errors.
     *
     * @param string $name   Error name
     * @param int    $line   Error line number
     * @param array  $errors Array with error messages
     *
     * @return void
     */
    public function addErrors(string $name, int $line, array $errors): void
    {
        foreach ($errors as $error) {
            $this->addError($name, $line, $error);
        }
    }

    /**
     * Add a function.
     *
     * @param FunctionReflectionInterface $fun Function reflection object
     *
     * @return void
     */
    public function addFunction(FunctionReflectionInterface $fun): void
    {
        $this->functions[$this->namespace . '\\' . $fun->getName()] = $fun;
    }

    /**
     * Set a class reflection object.
     *
     * @param ClassReflectionInterface $class Class reflection object
     *
     * @return void
     */
    public function enterClass(ClassReflectionInterface $class): void
    {
        $this->class = $class;
    }

    /**
     * Add a filename.
     *
     * @param string $file Filename
     * @param string $hash Hash string
     *
     * @return void
     */
    public function enterFile(string $file, string $hash): void
    {
        $this->file    = $file;
        $this->hash    = $hash;
        $this->errors  = [];
        $this->classes = [];
    }

    /**
     * Set a namespace name.
     *
     * @param string $namespace Namespace name
     *
     * @return void
     */
    public function enterNamespace(string $namespace): void
    {
        $this->namespace = $namespace;
        $this->aliases   = [];
    }

    /**
     * Returns all aliases.
     *
     * @return array
     */
    public function getAliases(): array
    {
        return $this->aliases;
    }

    /**
     * Returns the entered class reflection object.
     *
     * @return ClassReflectionInterface|null
     */
    public function getClass(): ?ClassReflectionInterface
    {
        return $this->class;
    }

    /**
     * Returns the doc-block parser object.
     *
     * @return DocBlockParser
     */
    public function getDocBlockParser(): DocBlockParser
    {
        return $this->docBlockParser;
    }

    /**
     * Returns all errors.
     *
     * @return ParserError[]
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * Returns the filename.
     *
     * @return string|null
     */
    public function getFile(): ?string
    {
        return $this->file;
    }

    /**
     * Returns the filter object.
     *
     * @return FilterInterface
     */
    public function getFilter(): FilterInterface
    {
        return $this->filter;
    }

    /**
     * Returns all functions.
     *
     * @return FunctionReflectionInterface[]
     */
    public function getFunctions(): array
    {
        return $this->functions;
    }

    /**
     * Returns the hash.
     *
     * @return string|null
     */
    public function getHash(): ?string
    {
        return $this->hash;
    }

    /**
     * Returns the entered namespace name.
     *
     * @return string|null
     */
    public function getNamespace(): ?string
    {
        return $this->namespace;
    }

    /**
     * Returns the pretty printer object.
     *
     * @return PrettyPrinter
     */
    public function getPrettyPrinter(): PrettyPrinter
    {
        return $this->prettyPrinter;
    }

    /**
     * Remove a class reflection object.
     *
     * @return void
     */
    public function leaveClass(): void
    {
        if (null === $this->class) {
            return;
        }

        $this->classes[] = $this->class;
        $this->class     = null;
    }

    /**
     * Remove the filename and errors and returns all classes.
     *
     * @return array
     */
    public function leaveFile(): array
    {
        $this->hash   = null;
        $this->file   = null;
        $this->errors = [];

        return $this->classes;
    }

    /**
     * Remove the entered namespace name.
     *
     * @return void
     */
    public function leaveNamespace(): void
    {
        $this->namespace = null;
        $this->aliases   = [];
    }
}
