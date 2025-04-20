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

use Azirax\Documentation\Parser\Node\DocBlockNode;
use phpDocumentor\Reflection\DocBlock\Tag;
use phpDocumentor\Reflection\DocBlock\Tags\Author;
use phpDocumentor\Reflection\DocBlock\Tags\Covers;
use phpDocumentor\Reflection\DocBlock\Tags\Deprecated;
use phpDocumentor\Reflection\DocBlock\Tags\Generic;
use phpDocumentor\Reflection\DocBlock\Tags\InvalidTag;
use phpDocumentor\Reflection\DocBlock\Tags\Link;
use phpDocumentor\Reflection\DocBlock\Tags\Param;
use phpDocumentor\Reflection\DocBlock\Tags\Property;
use phpDocumentor\Reflection\DocBlock\Tags\PropertyRead;
use phpDocumentor\Reflection\DocBlock\Tags\PropertyWrite;
use phpDocumentor\Reflection\DocBlock\Tags\Return_;
use phpDocumentor\Reflection\DocBlock\Tags\See;
use phpDocumentor\Reflection\DocBlock\Tags\Since;
use phpDocumentor\Reflection\DocBlock\Tags\Throws;
use phpDocumentor\Reflection\DocBlock\Tags\Uses;
use phpDocumentor\Reflection\DocBlock\Tags\Var_;
use phpDocumentor\Reflection\DocBlock\Tags\Version;
use phpDocumentor\Reflection\DocBlockFactory;
use phpDocumentor\Reflection\Types\Context;

use function explode;
use function get_class;
use function ltrim;
use function substr;

/**
 * Class to parse a doc-block.
 *
 * @package      Azirax\Documentation\Parser
 * @author       Rene Dziuba <php.tux@web.de>
 * @author       Fabien Potencier <fabien@symfony.com>
 * @copyright    Copyright (c) 2025 The Authors
 * @license      <https://opensource.org/license/mit> MIT License
 */
class DocBlockParser
{
    /**
     * Parse a doc-block.
     *
     * @param string|null   $comment Comment
     * @param ParserContext $context Parser context object
     *
     * @return DocBlockNode
     */
    public function parse(?string $comment, ParserContext $context): DocBlockNode
    {
        $docBlock     = null;
        $errorMessage = '';
        $result       = new DocBlockNode();

        if ($comment === null) {
            return $result;
        }

        try {
            $factory         = DocBlockFactory::createInstance();
            $docBlockContext = new Context(
                $context->getNamespace() ?? '',
                $context->getAliases() ?: [],
            );
            $docBlock        = $factory->create($comment, $docBlockContext);
        } catch (\Exception $e) {
            $errorMessage = $e->getMessage();
        }

        if ($errorMessage !== '') {
            $result->addError($errorMessage);

            return $result;
        }

        if ($docBlock === null) {
            $errorMessage = 'Unable to build the doc block factory, this should not happen. Please report it !';
            $result->addError($errorMessage);
            return $result;
        }

        $result->setShortDesc($docBlock->getSummary());
        $result->setLongDesc($docBlock->getDescription()->__toString());

        foreach ($docBlock->getTags() as $tag) {
            $result->addTag($tag->getName(), $this->parseTag($tag));
        }

        return $result;
    }

    /**
     * Parse hint.
     *
     * @param array $rawHints Hint data
     *
     * @return array
     */
    protected function parseHint(array $rawHints): array
    {
        $hints = [];
        foreach ($rawHints as $hint) {
            if (str_ends_with($hint, '[]')) {
                $hints[] = [substr($hint, 0, -2), true];
            } else {
                $hints[] = [$hint, false];
            }
        }

        return $hints;
    }

    /**
     * Parse a doc-block tag.
     *
     * @param Tag $tag Doc-block tag object
     *
     * @return array|string
     */
    protected function parseTag(Tag $tag): array|string
    {
        $class = get_class($tag);

        switch ($class) {
            case Var_::class:
            case Return_::class:
                /** @var Return_ $tag */
                $type = $tag->getType();
                return [
                    $this->parseHint($type !== null ? explode('|', $type->__toString()) : []),
                    $tag->getDescription() !== null ? $tag->getDescription()->__toString() : '',
                ];
            case Property::class:
            case PropertyRead::class:
            case PropertyWrite::class:
            case Param::class:
                /** @var Param $tag */
                $type = $tag->getType();
                return [
                    $this->parseHint($type !== null ? explode('|', $type->__toString()) : []),
                    ltrim($tag->getVariableName() ?? '', '$'),
                    $tag->getDescription() !== null ? $tag->getDescription()->__toString() : '',
                ];
            case Throws::class:
                /** @var Throws $tag */
                $type = $tag->getType();
                return [
                    $type !== null ? $type->__toString() : '',
                    $tag->getDescription() !== null ? $tag->getDescription()->__toString() : '',
                ];
            case See::class:
                // For backwards compatibility, in first cell we store content.
                // In second - only a referer for further parsing.
                // In docblock node we handle this in getOtherTags() method.
                /** @var See $tag */
                return [
                    $tag->__toString(),
                    $tag->getReference()->__toString(),
                    $tag->getDescription() !== null ? $tag->getDescription()->__toString() : '',
                ];
            case InvalidTag::class:
                /** @var InvalidTag $tag */
                return $tag->__toString();
            case Deprecated::class:
                /** @var Deprecated $tag */
                return $tag->__toString();
            case Covers::class:
                /** @var Covers $tag */
                return $tag->__toString();
            case Author::class:
                /** @var Author $tag */
                return $tag->__toString();
            case Version::class:
                /** @var Version $tag */
                return $tag->__toString();
            case Link::class:
                /** @var Link $tag */
                return $tag->__toString();
            case Since::class:
                /** @var Since $tag */
                return $tag->__toString();
            case Uses::class:
                /** @var Uses $tag */
                return $tag->__toString();
            case Generic::class:
                //TODO: better handling
                /** @var Generic $tag */
                return $tag->__toString();
            default:
                return $tag->__toString();
        }
    }
}
