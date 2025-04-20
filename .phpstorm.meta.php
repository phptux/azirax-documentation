<?php

namespace PHPSTORM_META {

    // Azirax services

    override(
        \Azirax\Documentation\Azirax::getService(),
        map([
            'codeParser'     => \Azirax\Documentation\Parser\CodeParser::class,
            'docBlockParser' => \Azirax\Documentation\Parser\DocBlockParser::class,
            'filter'         => \Azirax\Documentation\Parser\Filters\FilterInterface::class,
            'indexer'        => \Azirax\Documentation\Indexer::class,
            'parser'         => \Azirax\Documentation\Parser\Parser::class,
            'parserContext'  => \Azirax\Documentation\Parser\ParserContext::class,
            'phpTraverser'   => \PhpParser\NodeTraverser::class,
            'phpParser'      => \PhpParser\ParserFactory::class,
            'prettyPrinter'  => \PhpParser\PrettyPrinter::class,
            'project'        => \Azirax\Documentation\Project::class,
            'renderer'       => \Azirax\Documentation\Renderer\Renderer::class,
            'store'          => \Azirax\Documentation\Store\StoreInterface::class,
            'themes'         => \Azirax\Documentation\Renderer\ThemeSet::class,
            'traverser'      => \Azirax\Documentation\Parser\ProjectTraverser::class,
            'tree'           => \Azirax\Documentation\Tree::class,
            'twig'           => \Twig\Environment::class,
            'versions'       => \Azirax\Documentation\Version\VersionCollection::class,
        ]),
    );
}
