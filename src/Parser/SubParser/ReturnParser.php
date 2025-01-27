<?php

declare(strict_types=1);

namespace Elfennol\MonkeyPhp\Parser\SubParser;

use Elfennol\MonkeyPhp\Lexer\LexerInterface;
use Elfennol\MonkeyPhp\Node\Catalog\Stmt\ReturnNode;
use Elfennol\MonkeyPhp\Parser\Eater\TokenEater;
use Elfennol\MonkeyPhp\Parser\ExprParserInterface;
use Elfennol\MonkeyPhp\Parser\PrattParser\BindingPowerSet;
use Elfennol\MonkeyPhp\Token\TokenType;
use Elfennol\MonkeyPhp\Utils\Option\None;
use Elfennol\MonkeyPhp\Utils\Option\Some;

readonly class ReturnParser
{
    public function __construct(private TokenEater $tokenEater)
    {
    }

    public function parse(LexerInterface $lexer, ExprParserInterface $exprParser): ReturnNode
    {
        $nearToken = $lexer->current();
        $this->tokenEater->eat(TokenType::Return, $lexer);

        if (TokenType::Semicolon === $lexer->current()->type) {
            return new ReturnNode($nearToken, new None());
        }

        return new ReturnNode($nearToken, new Some($exprParser->parse($lexer, BindingPowerSet::MIN)));
    }
}
