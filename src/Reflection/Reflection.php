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

namespace Azirax\Documentation\Reflection;

use Azirax\Documentation\Project;

use function array_shift;
use function explode;
use function implode;
use function strtolower;
use function trim;

/**
 * Abstract reflection class.
 *
 * @package      Azirax\Documentation\Reflection
 * @author       Rene Dziuba <php.tux@web.de>
 * @author       Fabien Potencier <fabien@symfony.com>
 * @copyright    Copyright (c) 2025 The Authors
 * @license      <https://opensource.org/license/mit> MIT License
 */
abstract class Reflection
{
    /**
     * Array with PHP hint types.
     *
     * @var array
     */
    private static array $phpHints
        = [
            '',
            'array',
            'boolean',
            'bool',
            'callable',
            'double',
            'false',
            'float',
            'int',
            'integer',
            'iterable',
            'mixed',
            'never',
            'null',
            'object',
            'resource',
            'string',
            'scalar',
            '$this',
            'true',
            'void',
        ];

    /**
     * Error messages
     *
     * @var string[]
     */
    protected array $errors = [];

    /**
     * Flag for read-only.
     *
     * @var bool
     */
    protected bool $isReadOnly = false;

    /**
     * Line number
     *
     * @var int
     */
    protected int $line;

    /**
     * Name
     *
     * @var string
     */
    protected string $name;

    /**
     * Project object
     *
     * @var Project|null
     */
    protected ?Project $project = null;

    /**
     * Constructor for Reflection
     *
     * @param string $name Name
     * @param int    $line Line number
     */
    public function __construct(string $name, int $line)
    {
        $this->name = $name;
        $this->line = $line;
    }

    /**
     * Check, if the hint name a PHP hint type.
     *
     * @param string $hint Hint name
     *
     * @return bool
     */
    public static function isPhpHint(string $hint): bool
    {
        return in_array(strtolower($hint), self::$phpHints);
    }

    /**
     * Returns the errors as an array.
     *
     * @return array
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * Returns the line number.
     *
     * @return int
     */
    public function getLine(): int
    {
        return $this->line;
    }

    /**
     * Returns the name.
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Returns the API uri for the Phalcon Framework.
     *
     * @return string
     */
    public function getPhalconApiUri(): string
    {
        // Version number from config
        $version = $this->getProject()->getConfig('phalconVersion', 'latest');

        // explode the class name
        $sections = explode('\\', trim($this->getName(), '\\'));

        // First part is `phalcon`
        $first = array_shift($sections);

        // version-number/api/phalcon_db/#dbadapterabstractadapter
        return $version . '/api/'
            . strtolower($first . '_' . $sections[0]) . '/#'
            . strtolower(implode('', $sections));
    }

    /**
     * Returns the project object.
     *
     * @return Project|null
     */
    public function getProject(): ?Project
    {
        return $this->project;
    }

    /**
     * Read-only mode is enabled?
     *
     * @return bool
     */
    public function isReadOnly(): bool
    {
        return $this->isReadOnly;
    }

    /**
     * Set the errors.
     *
     * @param array $errors Array with error messages
     */
    public function setErrors(array $errors): void
    {
        $this->errors = $errors;
    }

    /**
     * Set the line number.
     *
     * @param int $line Line number
     */
    public function setLine(int $line): void
    {
        $this->line = $line;
    }

    /**
     * Set the name.
     *
     * @param string $name Name
     *
     * @return void
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * Set the project object.
     *
     * @param Project $project Project object
     */
    public function setProject(Project $project): void
    {
        $this->project = $project;
    }

    /**
     * Enable or disable the read-only mode.
     *
     * @param bool $isReadOnly Flag
     *
     * @return void
     */
    public function setReadOnly(bool $isReadOnly): void
    {
        $this->isReadOnly = $isReadOnly;
    }
}
