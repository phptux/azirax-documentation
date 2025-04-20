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

use PhpParser\Error;
use PhpParser\NodeTraverser;
use PhpParser\Parser as PhpParser;

use function ini_set;

/**
 * Code parser class.
 *
 * @package      Azirax\Documentation\Parser
 * @author       Rene Dziuba <php.tux@web.de>
 * @author       Fabien Potencier <fabien@symfony.com>
 * @copyright    Copyright (c) 2025 The Authors
 * @license      <https://opensource.org/license/mit> MIT License
 */
class CodeParser
{
    /**
     * Parser context object
     *
     * @var ParserContext
     */
    protected ParserContext $context;

    /**
     * PHP parser object
     *
     * @var PhpParser
     */
    protected PhpParser $parser;

    /**
     * Node traverser object
     *
     * @var NodeTraverser
     */
    protected NodeTraverser $traverser;

    /**
     * Constructor for CodeParser
     *
     * @param ParserContext $context   Parser context object
     * @param PhpParser     $parser    PHP parser object
     * @param NodeTraverser $traverser Node traverser object
     */
    public function __construct(ParserContext $context, PhpParser $parser, NodeTraverser $traverser)
    {
        $this->context   = $context;
        $this->parser    = $parser;
        $this->traverser = $traverser;

        // with big fluent interfaces it can happen that PHP-Parser's Traverser
        // exceeds the 100 recursions limit; we set it to 10000 to be sure.
        ini_set('xdebug.max_nesting_level', '10000');
    }

    /**
     * Returns the parser context object.
     *
     * @return ParserContext
     */
    public function getContext(): ParserContext
    {
        return $this->context;
    }

    /**
     * Parse the given PHP code.
     *
     * @param string $code PHP code
     *
     * @return void
     */
    public function parse(string $code): void
    {
        try {
            $this->traverser->traverse($this->parser->parse($code) ?? []);
        } catch (Error $e) {
            $this->context->addError($this->context->getFile(), 0, $e->getMessage());
        }
    }
}
