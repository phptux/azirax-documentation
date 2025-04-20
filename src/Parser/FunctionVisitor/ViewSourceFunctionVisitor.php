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

namespace Azirax\Documentation\Parser\FunctionVisitor;

use Azirax\Documentation\Parser\FunctionVisitorInterface;
use Azirax\Documentation\Reflection\Interfaces\FunctionReflectionInterface;
use Azirax\Documentation\RemoteRepository\AbstractRemoteRepository;

/**
 * View a source visitor for functions.
 *
 * @package      Azirax\Documentation\Parser\FunctionVisitor
 * @author       Rene Dziuba <php.tux@web.de>
 * @author       Fabien Potencier <fabien@symfony.com>
 * @copyright    Copyright (c) 2025 The Authors
 * @license      <https://opensource.org/license/mit> MIT License
 */
class ViewSourceFunctionVisitor implements FunctionVisitorInterface
{
    /**
     * Remote repository object
     *
     * @var AbstractRemoteRepository
     */
    protected AbstractRemoteRepository $remoteRepository;

    /**
     * Constructor for ViewSourceFunctionVisitor
     *
     * @param AbstractRemoteRepository $remoteRepository Remote repository object
     */
    public function __construct(AbstractRemoteRepository $remoteRepository)
    {
        $this->remoteRepository = $remoteRepository;
    }

    /**
     * Visit the function reflection object and make changes.
     *
     * @param FunctionReflectionInterface $function Function reflection object
     *
     * @return bool Function data was modified or not.
     */
    public function visit(FunctionReflectionInterface $function): bool
    {
        $filePath = $this->remoteRepository->getRelativePath($function->getFile() ?? '');

        if ($function->getRelativeFilePath() !== $filePath) {
            $function->setRelativeFilePath($filePath);

            return true;
        }

        return false;
    }

}
