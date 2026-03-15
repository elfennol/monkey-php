<?php

namespace Elfennol\MonkeyPhp\Tests;

use Elfennol\MonkeyPhp\ContainerBuilder;
use Elfennol\MonkeyPhp\Lexer\LexerBuilderInterface;
use Elfennol\MonkeyPhp\Lexer\LexerInterface;
use Elfennol\MonkeyPhp\Utils\String\StringBuilder;
use Elfennol\MonkeyPhp\Utils\String\StringUtils;

/**
 * @internal
 */
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
        return new ContainerBuilder()->build()->get(LexerBuilderInterface::class);
    }
}
