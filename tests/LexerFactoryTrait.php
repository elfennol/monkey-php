<?php

namespace Elfennol\MonkeyPhp\Tests;

use Elfennol\MonkeyPhp\Lexer\LexerBuilder;
use Elfennol\MonkeyPhp\Lexer\LexerBuilderInterface;
use Elfennol\MonkeyPhp\Lexer\LexerInterface;
use Elfennol\MonkeyPhp\Lexer\TokenBuilder;
use Elfennol\MonkeyPhp\Lexer\TokenTypeFinder;
use Elfennol\MonkeyPhp\Utils\String\StringBuilder;
use Elfennol\MonkeyPhp\Utils\String\StringUtils;

trait LexerFactoryTrait
{
    private function createLexer(string $input): LexerInterface
    {
        $stringUtils = new StringUtils();
        $stringBuilder = new StringBuilder($stringUtils);
        $stringIterator = $stringBuilder->build($input);
        $stringIterator->rewind();
        $lexer = $this->createLexerBuilder()->build($stringIterator);
        $lexer->rewind();

        return $lexer;
    }

    private function createLexerBuilder(): LexerBuilderInterface
    {
        $tokenFinder = new TokenBuilder(new TokenTypeFinder());

        return (new LexerBuilder($tokenFinder));
    }
}
