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
use Azirax\Documentation\RemoteRepository\AbstractRemoteRepository;

/**
 * View a source visitor for classes.
 *
 * @package      Azirax\Documentation\Parser\ClassVisitor
 * @author       Rene Dziuba <php.tux@web.de>
 * @author       Fabien Potencier <fabien@symfony.com>
 * @copyright    Copyright (c) 2025 The Authors
 * @license      <https://opensource.org/license/mit> MIT License
 */
class ViewSourceClassVisitor implements ClassVisitorInterface
{
    /**
     * Remote repository object
     *
     * @var AbstractRemoteRepository
     */
    protected AbstractRemoteRepository $remoteRepository;

    /**
     * Constructor for ViewSourceClassVisitor
     *
     * @param AbstractRemoteRepository $remoteRepository    Remote repository object
     */
    public function __construct(AbstractRemoteRepository $remoteRepository)
    {
        $this->remoteRepository = $remoteRepository;
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
        $filePath = $this->remoteRepository->getRelativePath($class->getFile());

        if ($class->getRelativeFilePath() != $filePath) {
            $class->setRelativeFilePath($filePath);

            return true;
        }

        return false;
    }

}
