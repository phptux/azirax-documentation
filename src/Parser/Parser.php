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

use Azirax\Documentation\Message;
use Azirax\Documentation\Project;
use Azirax\Documentation\Reflection\Interfaces\ClassReflectionInterface;
use Azirax\Documentation\Reflection\Interfaces\FunctionReflectionInterface;
use Azirax\Documentation\Reflection\LazyClassReflection;
use Azirax\Documentation\Store\StoreInterface;
use InvalidArgumentException;
use SplObjectStorage;
use Symfony\Component\Finder\Finder;
use Traversable;

use function call_user_func;

/**
 * Azirax parser.
 *
 * @package      Azirax\Documentation\Parser
 * @author       Rene Dziuba <php.tux@web.de>
 * @author       Fabien Potencier <fabien@symfony.com>
 * @copyright    Copyright (c) 2025 The Authors
 * @license      <https://opensource.org/license/mit> MIT License
 */
class Parser
{
    /**
     * Finder object
     *
     * @var Finder
     */
    protected Finder $iterator;

    /**
     * Code parser object
     *
     * @var CodeParser
     */
    protected CodeParser $parser;

    /**
     * Store object
     *
     * @var StoreInterface
     */
    protected StoreInterface $store;

    /**
     * Project traverser object
     *
     * @var ProjectTraverser
     */
    protected ProjectTraverser $traverser;

    /**
     * Constructor for Parser
     *
     * @param Finder|string    $iterator  Finder object or full directory path
     * @param StoreInterface   $store     Store object
     * @param CodeParser       $parser    Code parser object
     * @param ProjectTraverser $traverser Project traverser object
     */
    public function __construct(Finder|string $iterator, StoreInterface $store, CodeParser $parser, ProjectTraverser $traverser)
    {
        $this->iterator  = $this->createIterator($iterator);
        $this->store     = $store;
        $this->parser    = $parser;
        $this->traverser = $traverser;
    }

    /**
     * Parse the project files.
     *
     * @param Project       $project  Project object
     * @param callable|null $callback Callback function
     *
     * @return Transaction
     */
    public function parse(Project $project, ?callable $callback = null): Transaction
    {
        $step        = 0;
        $steps       = iterator_count($this->iterator);
        $context     = $this->parser->getContext();
        $transaction = new Transaction($project);
        $toStore     = new SplObjectStorage();
        foreach ($this->iterator as $file) {
            $file = $file->getPathname();
            ++$step;

            $code = file_get_contents($file);
            if ($code === false) {
                continue;
            }

            $hash = sha1($code);
            if ($transaction->hasHash($hash)) {
                continue;
            }

            $context->enterFile((string)$file, $hash);

            $this->parser->parse($code);

            if (null !== $callback) {
                call_user_func($callback, Message::PARSE_ERROR, $context->getErrors());
            }

            foreach ($context->getFunctions() as $addr => $fun) {
                $project->addFunction($fun);
                $toStore->attach($fun);
            }

            foreach ($context->leaveFile() as $class) {
                if (null !== $callback) {
                    call_user_func($callback, Message::PARSE_CLASS, [$step, $steps, $class]);
                }

                $project->addClass($class);
                $transaction->addClass($class);
                $toStore->attach($class);
                $class->notFromCache();
            }
        }

        // cleanup
        foreach ($transaction->getRemovedClasses() as $class) {
            $project->removeClass(new LazyClassReflection($class));
            $this->store->removeClass($project, $class);
        }

        // visit each class for stuff that can only be done when all classes are parsed
        $toStore->addAll($this->traverser->traverse($project));

        foreach ($toStore as $classOrFun) {
            if ($classOrFun instanceof FunctionReflectionInterface) {
                $this->store->writeFunction($project, $classOrFun);
            } elseif ($classOrFun instanceof ClassReflectionInterface) {
                $this->store->writeClass($project, $classOrFun);
            }
        }

        return $transaction;
    }

    /**
     * Create and return the file iterator.
     *
     * @param string|Finder $iterator Finder object or full directory path
     *
     * @return Finder
     */
    private function createIterator(Finder|string $iterator): Finder
    {
        if (is_string($iterator)) {
            $it = new Finder();
            $it->files()->name('*.php')->in($iterator);

            return $it;
        } elseif (!$iterator instanceof Traversable) {
            throw new InvalidArgumentException('The iterator must be a directory name or a Finder instance.');
        }

        return $iterator;
    }
}
