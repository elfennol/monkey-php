<?php

declare(strict_types=1);

namespace Elfennol\MonkeyPhp\Parser\SubParser;

use Elfennol\MonkeyPhp\Lexer\LexerInterface;
use Elfennol\MonkeyPhp\Node\Catalog\Expr\IdentifierNode;
use Elfennol\MonkeyPhp\Node\Catalog\Stmt\LetNode;
use Elfennol\MonkeyPhp\Parser\Eater\TokenEater;
use Elfennol\MonkeyPhp\Parser\ExprParserInterface;
use Elfennol\MonkeyPhp\Parser\PrattParser\BindingPowerSet;
use Elfennol\MonkeyPhp\Token\TokenType;

readonly class LetParser
{
    public function __construct(private TokenEater $tokenEater)
    {
    }

    public function parse(LexerInterface $lexer, ExprParserInterface $exprParser): LetNode
    {
        $nearToken = $lexer->current();
        $this->tokenEater->eat(TokenType::Let, $lexer);
        $identifier = $this->identifier($lexer);
        $this->tokenEater->eat(TokenType::Assign, $lexer);

        return new LetNode($nearToken, $identifier, $exprParser->parse($lexer, BindingPowerSet::MIN));
    }

    private function identifier(LexerInterface $lexer): IdentifierNode
    {
        $identifier = new IdentifierNode($lexer->current());
        $this->tokenEater->eat(TokenType::Identifier, $lexer);

        return $identifier;
    }
}
