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
use Azirax\Documentation\Reflection\ClassReflection;
use Azirax\Documentation\Reflection\ConstantsReflection;
use Azirax\Documentation\Reflection\FunctionReflection;
use Azirax\Documentation\Reflection\Interfaces\ClassReflectionInterface;
use Azirax\Documentation\Reflection\Interfaces\FunctionReflectionInterface;
use Azirax\Documentation\Reflection\Interfaces\MethodReflectionInterface;
use Azirax\Documentation\Reflection\Interfaces\ParameterReflectionInterface;
use Azirax\Documentation\Reflection\Interfaces\PropertyReflectionInterface;
use Azirax\Documentation\Reflection\Interfaces\ReflectionInterface;
use Azirax\Documentation\Reflection\MethodReflection;
use Azirax\Documentation\Reflection\ParameterReflection;
use Azirax\Documentation\Reflection\PropertyReflection;
use Azirax\Documentation\Reflection\Reflection;
use PhpParser\Node as AbstractNode;
use PhpParser\Node\Expr as NodeExpr;
use PhpParser\Node\Expr\Error as ExprError;
use PhpParser\Node\IntersectionType;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\Node\NullableType;
use PhpParser\Node\Stmt\Class_ as ClassNode;
use PhpParser\Node\Stmt\ClassConst as ClassConstNode;
use PhpParser\Node\Stmt\ClassLike as ClassLikeNode;
use PhpParser\Node\Stmt\ClassMethod as ClassMethodNode;
use PhpParser\Node\Stmt\Function_ as FunctionNode;
use PhpParser\Node\Stmt\Interface_ as InterfaceNode;
use PhpParser\Node\Stmt\Namespace_ as NamespaceNode;
use PhpParser\Node\Stmt\Property as PropertyNode;
use PhpParser\Node\Stmt\PropertyProperty;
use PhpParser\Node\Stmt\Trait_ as TraitNode;
use PhpParser\Node\Stmt\Enum_ as EnumNode;
use PhpParser\Node\Stmt\EnumCase as EnumCaseNode;
use PhpParser\Node\Stmt\TraitUse as TraitUseNode;
use PhpParser\Node\Stmt\Use_ as UseNode;
use PhpParser\Node\UnionType;
use PhpParser\NodeAbstract;
use PhpParser\NodeVisitorAbstract;

use function count;
use function explode;
use function implode;
use function is_array;
use function is_string;
use function sprintf;
use function stripos;

/**
 * Node visitor class.
 *
 * @package      Azirax\Documentation\Parser
 * @author       Rene Dziuba <php.tux@web.de>
 * @author       Fabien Potencier <fabien@symfony.com>
 * @copyright    Copyright (c) 2025 The Authors
 * @license      <https://opensource.org/license/mit> MIT License
 */
class NodeVisitor extends NodeVisitorAbstract
{
    /**
     * Parser context object
     *
     * @var ParserContext
     */
    protected ParserContext $context;

    /**
     * Constructor for NodeVisitor
     *
     * @param ParserContext $context Parser context object
     */
    public function __construct(ParserContext $context)
    {
        $this->context = $context;
    }

    /**
     * Enter a node.
     *
     * @param AbstractNode $node Node object
     *
     * @return null
     */
    public function enterNode(AbstractNode $node)
    {
        if ($node instanceof NamespaceNode) {
            $this->context->enterNamespace($node->name === null ? '' : $node->name->__toString());
        } elseif ($node instanceof UseNode) {
            $this->addAliases($node);
        } elseif ($node instanceof InterfaceNode) {
            $this->addInterface($node);
        } elseif ($node instanceof ClassNode) {
            $this->addClass($node);
        } elseif ($node instanceof TraitNode) {
            $this->addTrait($node);
        } elseif ($node instanceof EnumNode) {
            $this->addEnum($node);
        } elseif ($node instanceof FunctionNode) {
            $this->addFunction($node, $this->context->getNamespace());
        } elseif ($this->context->getClass() && $node instanceof TraitUseNode) {
            $this->addTraitUse($node);
        } elseif ($this->context->getClass() && $node instanceof PropertyNode) {
            $this->addProperty($node);
        } elseif ($this->context->getClass() && $node instanceof ClassMethodNode) {
            $this->addMethod($node);
        } elseif ($this->context->getClass() && $node instanceof ClassConstNode) {
            $this->addConstant($node);
        }

        return null;
    }

    /**
     * Leave a node.
     *
     * @param AbstractNode $node Node object
     *
     * @return null
     */
    public function leaveNode(AbstractNode $node)
    {
        if ($node instanceof NamespaceNode) {
            $this->context->leaveNamespace();
        } elseif ($node instanceof ClassNode || $node instanceof InterfaceNode || $node instanceof TraitNode || $node instanceof EnumNode) {
            $this->context->leaveClass();
        }

        return null;
    }

    /**
     * Add aliases.
     *
     * @param UseNode $node Node object
     *
     * @return void
     */
    protected function addAliases(UseNode $node): void
    {
        foreach ($node->uses as $use) {
            $alias    = $use->getAlias()->toString();
            $fullName = $use->name->__toString();
            $this->context->addAlias($alias, $fullName);
        }
    }

    /**
     * Add enum.
     *
     * @param EnumNode $node    Enum node object
     *
     * @return void
     */
    protected function addEnum(EnumNode $node): void
    {
        $class = $this->addClassOrInterface($node);
        $class->makeEnum();

        // Add cases
        foreach ($node->stmts as $case) {
            $constant   = new ConstantsReflection($case->name->toString(), $case->getLine());
            $docComment = $case->getDocComment();
            $docComment = $docComment === null ? null : $docComment->__toString();
            $comment    = $this->context->getDocBlockParser()->parse($docComment, $this->context);
            $constant->setDocComment($docComment);
            $constant->setShortDesc($comment->getShortDesc());
            $constant->setLongDesc($comment->getLongDesc());
            $constant->setTags($comment->getOtherTags());

            $class->addConstant($constant);
        }
    }

    /**
     * Add class.
     *
     * @param ClassNode $node Class node object
     *
     * @return void
     */
    protected function addClass(ClassNode $node): void
    {
        // Skip anonymous classes
        if ($node->isAnonymous()) {
            return;
        }

        $class = $this->addClassOrInterface($node);

        foreach ($node->implements as $interface) {
            $class->addInterface((string)$interface);
        }

        if ($node->extends) {
            $class->setParent((string)$node->extends);
        }
    }

    /**
     * Add a class or an interface.
     *
     * @param ClassLikeNode $node Node object
     *
     * @return ClassReflectionInterface
     */
    protected function addClassOrInterface(ClassLikeNode $node): ClassReflectionInterface
    {
        $class = new ClassReflection((string)$node->namespacedName, $node->getLine());

        return $this->addClassOrInterfaceForReflection($class, $node);
    }

    /**
     * Add data for class or interface.
     *
     * @param ClassReflection $class Class reflection object
     * @param ClassLikeNode   $node  Node object
     *
     * @return ClassReflection
     */
    protected function addClassOrInterfaceForReflection(ClassReflection $class, ClassLikeNode $node): ClassReflection
    {
        if ($node instanceof ClassNode) {
            $class->setModifier($node->flags);
        }
        $class->setNamespace($this->context->getNamespace() ?? '');
        $class->setAliases($this->context->getAliases());
        $class->setHash($this->context->getHash());
        $class->setFile($this->context->getFile());

        $docComment = $node->getDocComment();
        $docComment = $docComment === null ? null : $docComment->__toString();
        $comment    = $this->context->getDocBlockParser()->parse($docComment, $this->context);
        $class->setDocComment($docComment);
        $class->setShortDesc($comment->getShortDesc());
        $class->setLongDesc($comment->getLongDesc());
        $class->setSee($this->resolveSee($comment->getTag('see')));
        if ($errors = $comment->getErrors()) {
            $class->setErrors($errors);
        } else {
            $otherTags = $comment->getOtherTags();
            if (isset($otherTags['readonly'])) {
                $class->setReadOnly(true);
            }
            $class->setTags($otherTags);
        }

        if ($this->context->getFilter()->acceptClass($class)) {
            if ($errors) {
                $this->context->addErrors((string)$class, $node->getLine(), $errors);
            }
            $this->context->enterClass($class);
        }

        $class->setModifiersFromTags();

        return $class;
    }

    /**
     * Add constants.
     *
     * @param ClassConstNode $node Constants node object
     *
     * @return void
     */
    protected function addConstant(ClassConstNode $node): void
    {
        foreach ($node->consts as $const) {
            $constant   = new ConstantsReflection($const->name->toString(), $const->getLine());
            $docComment = $node->getDocComment();
            $docComment = $docComment === null ? null : $docComment->__toString();
            $comment    = $this->context->getDocBlockParser()->parse($docComment, $this->context, $constant);
            $constant->setDocComment($docComment);
            $constant->setShortDesc($comment->getShortDesc());
            $constant->setLongDesc($comment->getLongDesc());
            $constant->setTags($comment->getOtherTags());
            $constant->setModifier($node->flags);
            $constant->setModifiersFromTags();
            if (isset($node->type->name)) {
                $constant->setHint($node->type->name);
            }
            $constant->setValue($this->context->getPrettyPrinter()->prettyPrintExpr($const->value));

            $this->context->getClass()->addConstant($constant);
        }
    }

    /**
     * Add a function.
     *
     * @param FunctionNode $node      Node object
     * @param string|null  $namespace Namespace name
     *
     * @return void
     */
    protected function addFunction(FunctionNode $node, ?string $namespace = null): void
    {
        $function = new FunctionReflection($node->name->__toString(), $node->getLine());
        $function->setNamespace($namespace !== null ? $namespace : '');
        $function->setByRef((bool)$node->byRef);
        $function->setFile($this->context->getFile());

        foreach ($node->params as $param) {
            if ($param->var instanceof ExprError) {
                $errors = [
                    'The expression had an error, please report this to Azirax for a better handling of this error',
                ];
                $this->context->addErrors($function->__toString(), $node->getLine(), $errors);
                continue;
            }
            if ($param->var->name instanceof NodeExpr) {
                $errors = [
                    'This was unexpected, please report this to Azirax for a better handling of this error',
                ];
                $this->context->addErrors($function->__toString(), $node->getLine(), $errors);
                continue;
            }
            $parameter = new ParameterReflection(
                $param->var->name,
                $param->getLine(),
            );
            $parameter->setModifier($param->flags);
            $parameter->setByRef($param->byRef);
            if ($param->default) {
                $parameter->setDefault($this->context->getPrettyPrinter()->prettyPrintExpr($param->default));
            }

            $parameter->setVariadic($param->variadic);

            $this->manageHint($param->type, $parameter);

            $function->addParameter($parameter);
        }

        $docComment = $node->getDocComment();
        $docComment = $docComment === null ? null : $docComment->__toString();
        $comment    = $this->context->getDocBlockParser()->parse($docComment, $this->context);
        $function->setDocComment($docComment);
        $function->setShortDesc($comment->getShortDesc());
        $function->setLongDesc($comment->getLongDesc());
        $function->setSee($this->resolveSee($comment->getTag('see')));
        if (!$errors = $comment->getErrors()) {
            $errors = $this->updateMethodParametersFromTags($function, $comment->getTag('param'));

            $this->addTagFromCommentToMethod('return', $comment, $function, $errors);

            $function->setExceptions($comment->getTag('throws'));
            $function->setTags($comment->getOtherTags());
        }


        $function->setModifiersFromTags();
        $function->setErrors($errors);

        $this->manageHint($node->getReturnType(), $function);

        $this->context->addFunction($function);

        if ($errors) {
            $this->context->addErrors((string)$function, $node->getLine(), $errors);
        }
    }

    /**
     * Add interface.
     *
     * @param InterfaceNode $node Interface node object
     *
     * @return void
     */
    protected function addInterface(InterfaceNode $node): void
    {
        $class = $this->addClassOrInterface($node);
        $class->makeInterface();

        foreach ($node->extends as $interface) {
            $class->addInterface((string)$interface);
        }
    }

    /**
     * Add a method.
     *
     * @param ClassMethodNode $node
     *
     * @return void
     */
    protected function addMethod(ClassMethodNode $node): void
    {
        $method = new MethodReflection($node->name->__toString(), $node->getLine());
        $method->setModifier($node->flags);
        $method->setByRef((bool)$node->byRef);

        foreach ($node->params as $param) {
            if ($param->var instanceof ExprError) {
                $errors = [
                    'The expression had an error, please report this to Azirax for a better handling of this error',
                ];
                $this->context->addErrors($method->__toString(), $node->getLine(), $errors);
                continue;
            }
            if ($param->var->name instanceof NodeExpr) {
                $errors = [
                    'This was unexpected, please report this to Azirax for a better handling of this error',
                ];
                $this->context->addErrors($method->__toString(), $node->getLine(), $errors);
                continue;
            }
            $parameter = new ParameterReflection($param->var->name, $param->getLine());
            $parameter->setModifier($param->flags);
            $parameter->setByRef($param->byRef);
            if ($param->default) {
                $parameter->setDefault($this->context->getPrettyPrinter()->prettyPrintExpr($param->default));
            }

            $parameter->setVariadic($param->variadic);

            $this->manageHint($param->type, $parameter);

            $method->addParameter($parameter);
        }

        $docComment = $node->getDocComment();
        $docComment = $docComment === null ? null : $docComment->__toString();
        $comment    = $this->context->getDocBlockParser()->parse(
            $docComment,
            $this->context,
            $method,
        );
        $method->setDocComment($docComment);
        $method->setShortDesc($comment->getShortDesc());
        $method->setLongDesc($comment->getLongDesc());
        $method->setSee($this->resolveSee($comment->getTag('see')));
        if (!$errors = $comment->getErrors()) {
            $errors = $this->updateMethodParametersFromTags($method, $comment->getTag('param'));

            $this->addTagFromCommentToMethod('return', $comment, $method, $errors);

            $method->setExceptions($comment->getTag('throws'));
            $method->setTags($comment->getOtherTags());
        }

        $method->setModifiersFromTags();
        $method->setErrors($errors);

        $this->manageHint($node->getReturnType(), $method);

        if ($this->context->getFilter()->acceptMethod($method)) {
            $this->context->getClass()->addMethod($method);

            if ($errors) {
                $this->context->addErrors((string)$method, $node->getLine(), $errors);
            }
        }
    }

    /**
     * Add a property.
     *
     * @param PropertyNode $node Property node object
     *
     * @return void
     */
    protected function addProperty(PropertyNode $node): void
    {
        foreach ($node->props as $prop) {
            [$property, $errors] = $this->getPropertyReflectionFromParserProperty($node, $prop);

            if ($this->context->getFilter()->acceptProperty($property)) {
                $this->context->getClass()->addProperty($property);

                if ($errors) {
                    $this->context->addErrors((string)$property, $prop->getLine(), $errors);
                }
            }
        }
    }

    /**
     * Add a tag from comment.
     *
     * @param string                                                                            $tagName                    Tag name
     * @param DocBlockNode                                                                      $comment                    Doc-block node object
     * @param FunctionReflectionInterface|MethodReflectionInterface|PropertyReflectionInterface $methodOrFunctionOrProperty Reflection object
     * @param array                                                                             $errors                     Array with error messages
     */
    protected function addTagFromCommentToMethod(
        string $tagName,
        DocBlockNode $comment,
        FunctionReflectionInterface|MethodReflectionInterface|PropertyReflectionInterface $methodOrFunctionOrProperty,
        array &$errors,
    ): void {
        $tagsThatShouldHaveOnlyOne = ['return', 'var'];
        $tag                       = $comment->getTag($tagName);
        if (!empty($tag)) {
            if (in_array($tagName, $tagsThatShouldHaveOnlyOne, true) && count($tag) > 1) {
                $errors[] = sprintf(
                    'Too much @%s tags on "%s" at @%s found: %d @%s tags',
                    $tagName,
                    $methodOrFunctionOrProperty->getName(),
                    $tagName,
                    count($tag),
                    $tagName,
                );
            }
            $firstTagFound = $tag[0] ?? null;
            if ($firstTagFound !== null) {
                if (is_array($firstTagFound)) {
                    $hint            = $firstTagFound[0];
                    $hintDescription = $firstTagFound[1] ?? null;
                    if (is_array($hint) && isset($hint[0]) && stripos($hint[0][0] ?? '', '&') !== false) {// Detect intersection type
                        $methodOrFunctionOrProperty->setIntersectionType(true);
                        $intersectionParts = explode('&', $hint[0][0]);
                        $hint              = [];
                        foreach ($intersectionParts as $part) {
                            $hint[] = [$part, false];
                        }
                    }
                    $methodOrFunctionOrProperty->setHint(is_array($hint) ? $this->resolveHint($hint) : $hint);
                    if ($hintDescription !== null) {
                        if (is_string($hintDescription)) {
                            $methodOrFunctionOrProperty->setHintDesc($hintDescription);
                        } else {
                            $errors[] = sprintf(
                                'The hint description on "%s" at @%s is invalid: "%s"',
                                $methodOrFunctionOrProperty->getName(),
                                $tagName,
                                $hintDescription,
                            );
                        }
                    }
                } else {
                    $errors[] = sprintf(
                        'The hint on "%s" at @%s is invalid: "%s"',
                        $methodOrFunctionOrProperty->getName(),
                        $tagName,
                        $firstTagFound,
                    );
                }
            }
        }
    }

    /**
     * Add trait.
     *
     * @param TraitNode $node Trait node object
     *
     * @return void
     */
    protected function addTrait(TraitNode $node): void
    {
        $class = $this->addClassOrInterface($node);

        $class->makeTrait();
    }

    /**
     * Add trait uses.
     *
     * @param TraitUseNode $node Trait use node object
     *
     * @return void
     */
    protected function addTraitUse(TraitUseNode $node): void
    {
        foreach ($node->traits as $trait) {
            $this->context->getClass()->addTrait((string)$trait);
        }
    }

    /**
     * Parse a `see` tag.
     *
     * @param string $reference   Reference
     * @param string $description Description
     *
     * @return array
     */
    protected function getParsedSeeEntry(string $reference, string $description): array
    {
        $matches = [];

        if (preg_match('/^[\w]+:\/\/.+$/', $reference) > 0) { //URL
            return [
                $reference,
                $description,
                false,
                false,
                $reference,
            ];
        } elseif (preg_match('/(.+)\:\:(.+)\(.*\)/', $reference, $matches) > 0) { //Method
            return [
                $reference,
                $description,
                $this->resolveAlias($matches[1]),
                $matches[2],
                false,
            ];
        } else { // We assume that this is a class reference.
            return [
                $reference,
                $description,
                $this->resolveAlias($reference),
                false,
                false,
            ];
        }
    }

    /**
     * Add the property data from the property node object.
     *
     * @param PropertyNode     $node Property node object
     * @param PropertyProperty $prop Property reflection object
     *
     * @return PropertyReflectionInterface[]
     */
    protected function getPropertyReflectionFromParserProperty(PropertyNode $node, PropertyProperty $prop): array
    {
        $property = new PropertyReflection($prop->name->toString(), $prop->getLine());
        $property->setModifier($node->flags);

        $property->setDefault($prop->default);

        $docComment = $node->getDocComment();
        $docComment = $docComment === null ? null : $docComment->__toString();
        $comment    = $this->context->getDocBlockParser()->parse($docComment, $this->context, $property);
        $property->setDocComment($docComment);
        $property->setShortDesc($comment->getShortDesc());
        $property->setLongDesc($comment->getLongDesc());
        $property->setSee($this->resolveSee($comment->getTag('see')));

        $this->manageHint($node->type, $property);

        if ($errors = $comment->getErrors()) {
            $property->setErrors($errors);
        } else {
            $this->addTagFromCommentToMethod('var', $comment, $property, $errors);
            $otherTags = $comment->getOtherTags();
            if (isset($otherTags['readonly'])) {
                $property->setReadOnly(true);
            }
            $property->setTags($otherTags);
        }
        $property->setModifiersFromTags();

        return [$property, $errors];
    }

    /**
     * Manage the hints.
     *
     * @param NodeAbstract|null   $type   Type declaration
     * @param ReflectionInterface $object Reflection object
     */
    protected function manageHint(?NodeAbstract $type, ReflectionInterface $object): void
    {
        if ($type instanceof IntersectionType) {
            $object->setIntersectionType(true);

            $typeArr = [];
            foreach ($type->types as $type) {
                $typeStr   = $this->typeToString($type);
                $typeArr[] = [$typeStr, false];
            }

            $object->setHint($this->resolveHint($typeArr));
        } else {
            $typeStr = $this->typeToString($type);

            if (null !== $typeStr) {
                $typeArr = [[$typeStr, false]];

                if ($type instanceof NullableType) {
                    $typeArr[] = ['null', false];
                }
                $object->setHint($this->resolveHint($typeArr));
            }
        }
    }

    /**
     * Resolve alias name.
     *
     * @param string $alias Alias name
     *
     * @return string
     */
    protected function resolveAlias(string $alias): string
    {
        // not a class
        if (Reflection::isPhpHint($alias)) {
            return $alias;
        }

        // FQCN
        if (str_starts_with($alias, '\\')) {
            return $alias;
        }

        $class = $this->context->getClass();

        // A class MIGHT or MIGHT NOT be present in context.
        // It is not present in cases, where eg. `@see` tag refers to non existing class/method.
        // We may want to run class related checks only, if class is actually present.
        if ($class) {
            // special aliases
            if ('self' === $alias || 'static' === $alias || '\$this' === $alias) {
                return $class->getName();
            }

            // an alias defined by a use statement
            $aliases = $class->getAliases();

            if (isset($aliases[$alias])) {
                return $aliases[$alias];
            }

            // a class in the current class namespace
            return $class->getNamespace() . '\\' . $alias;
        }

        return $alias;
    }

    /**
     * Resolve the hints.
     *
     * @param array $hints Array with hints
     *
     * @return array
     */
    protected function resolveHint(array $hints): array
    {
        foreach ($hints as $i => $hint) {
            $hints[$i] = [$this->resolveAlias($hint[0]), $hint[1]];
        }

        return $hints;
    }

    /**
     * Resolve a `see` tag.
     *
     * @param array $see Tag data
     *
     * @return array
     */
    protected function resolveSee(array $see): array
    {
        $return = [];
        foreach ($see as $seeEntry) {
            // Example: @see Net_Sample::$foo, Net_Other::someMethod()
            if (is_string($seeEntry)) {// Support bad formatted @see tags
                $seeEntries = explode(',', $seeEntry);
                foreach ($seeEntries as $entry) {
                    $return[] = $this->getParsedSeeEntry(trim($entry, " \n\t"), '');
                }
                continue;
            }
            $reference   = $seeEntry[1];
            $description = $seeEntry[2] ?? '';
            $return[]    = $this->getParsedSeeEntry($reference, $description);
        }

        return $return;
    }

    /**
     * Make type to string.
     *
     * @param NodeAbstract|null $type Type declaration
     *
     * @return string|null
     */
    protected function typeToString(?NodeAbstract $type): ?string
    {
        $typeString = null;

        if ($type !== null && !($type instanceof NullableType || $type instanceof UnionType || $type instanceof IntersectionType)) {
            $typeString = $type->__toString();
        } elseif ($type instanceof NullableType) {
            $typeString = $type->type->__toString();
        } elseif ($type instanceof UnionType) {
            $typeString = [];
            foreach ($type->types as $type) {
                $typeString[] = $type->__toString();
            }
            $typeString = implode('|', $typeString);
        } elseif ($type instanceof IntersectionType) {
            $typeString = [];
            foreach ($type->types as $type) {
                $typeAsStr = $type->__toString();
                if ($type instanceof FullyQualified && !str_starts_with($typeAsStr, '\\')) {
                    $typeAsStr = '\\' . $typeAsStr;
                }
                $typeString[] = $typeAsStr;
            }
            return implode('&', $typeString);
        }

        if ($typeString === null) {
            return null;
        }

        if ($type instanceof FullyQualified && !str_starts_with($typeString, '\\')) {
            $typeString = '\\' . $typeString;
        }

        return $typeString;
    }

    /**
     * Update the method parameters from the tags.
     *
     * @param FunctionReflectionInterface|MethodReflectionInterface $method Function or method reflection object
     * @param array                                                 $tags   Array with tags
     *
     * @return array
     */
    protected function updateMethodParametersFromTags(FunctionReflectionInterface|MethodReflectionInterface $method, array $tags): array
    {
        $errors = [];

        // bypass if there is no @param tags defined (@param tags are optional)
        if (!count($tags)) {
            return $errors;
        }

        /** @var ParameterReflectionInterface[] $parameters */
        $parameters = $method->getParameters();

        foreach ($parameters as $parameter) {
            $tag = $this->findParameterInTags($tags, $parameter->getName());
            if (!$parameter->hasHint() && $tag === null) {
                $errors[] = sprintf(
                    'The "%s" parameter of the method "%s" is missing a @param tag',
                    $parameter->getName(),
                    $method->getName(),
                );
                continue;
            }

            if ($tag !== null) {
                $parameter->setShortDesc($tag[2]);
                if (!$parameter->hasHint()) {
                    $parameter->setHint($this->resolveHint($tag[0]));
                }
            }
        }

        if (count($tags) > count($parameters)) {
            $errors[] = sprintf(
                'The method "%s" has "%d" @param tags but only "%d" where expected.',
                $method->getName(),
                count($tags),
                count($method->getParameters()),
            );
        }

        $invalidTags = $this->getInvalidTags($tags);
        if (count($invalidTags) > 0) {
            $errors[] = sprintf(
                'The method "%s" has "%d" invalid @param tags.',
                $method->getName(),
                count($invalidTags),
            );
            foreach ($invalidTags as $invalidTag) {
                $errors[] = sprintf(
                    'Invalid @param tag on "%s": "%s"',
                    $method->getName(),
                    $invalidTag,
                );
            }
        }
        return $errors;
    }

    /**
     * Find parameter in the tags.
     *
     * @param array  $tags    Array with tags
     * @param string $tagName Tag name
     *
     * @return array|null
     */
    private function findParameterInTags(array $tags, string $tagName): ?array
    {
        foreach ($tags as $tag) {
            if (!is_array($tag)) {
                continue;
            }
            if (count($tag) < 2) {
                continue;
            }
            if ($tag[1] === $tagName) {
                return $tag;
            }
        }
        return null;
    }

    /**
     * Returns invalid tags.
     *
     * @param array $tags All the tags
     *
     * @return array The invalid tags
     */
    private function getInvalidTags(array $tags): array
    {
        $invalidTags = [];
        foreach ($tags as $tag) {
            if (!is_array($tag)) {
                $invalidTags[] = $tag;
            }
        }
        return $invalidTags;
    }
}
