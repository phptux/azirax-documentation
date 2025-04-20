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

namespace Azirax\Documentation\Reflection\Interfaces;

use Azirax\Documentation\Project;

/**
 * Interface for the abstract Reflection class.
 *
 * @package      Azirax\Documentation\Reflection\Interfaces
 * @author       Rene Dziuba <php.tux@web.de>
 * @author       Fabien Potencier <fabien@symfony.com>
 * @copyright    Copyright (c) 2025 The Authors
 * @license      <https://opensource.org/license/mit> MIT License
 */
interface ReflectionInterface
{
    /**
     * Returns the errors as an array.
     *
     * @return array
     */
    public function getErrors(): array;

    /**
     * Returns the line number.
     *
     * @return int
     */
    public function getLine(): int;

    /**
     * Returns the name.
     *
     * @return string
     */
    public function getName(): string;

    /**
     * Returns the API uri for the Phalcon Framework.
     *
     * @return string
     */
    public function getPhalconApiUri(): string;

    /**
     * Returns the project object.
     *
     * @return Project|null
     */
    public function getProject(): ?Project;

    /**
     * Read-only mode is enabled?
     *
     * @return bool
     */
    public function isReadOnly(): bool;

    /**
     * Set the errors.
     *
     * @param array $errors Array with error messages
     */
    public function setErrors(array $errors): void;

    /**
     * Set the line number.
     *
     * @param int $line Line number
     */
    public function setLine(int $line): void;

    /**
     * Set the name.
     *
     * @param string $name Name
     *
     * @return void
     */
    public function setName(string $name): void;

    /**
     * Set the project object.
     *
     * @param Project $project Project object
     */
    public function setProject(Project $project): void;

    /**
     * Enable or disable the read-only mode.
     *
     * @param bool $isReadOnly Flag
     *
     * @return void
     */
    public function setReadOnly(bool $isReadOnly): void;
}
