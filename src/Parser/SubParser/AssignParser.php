<?php

declare(strict_types=1);

namespace Elfennol\MonkeyPhp\Parser\SubParser;

use Elfennol\MonkeyPhp\Lexer\LexerInterface;
use Elfennol\MonkeyPhp\Node\Catalog\Expr\AssignNode;
use Elfennol\MonkeyPhp\Node\Catalog\Expr\IdentifierNode;
use Elfennol\MonkeyPhp\Parser\Eater\TokenEater;
use Elfennol\MonkeyPhp\Parser\ExprParserInterface;
use Elfennol\MonkeyPhp\Parser\PrattParser\BindingPowerSet;
use Elfennol\MonkeyPhp\Token\TokenType;

readonly class AssignParser
{
    public function __construct(private TokenEater $tokenEater)
    {
    }

    public function parse(
        LexerInterface $lexer,
        ExprParserInterface $exprParser,
        IdentifierNode $identifier
    ): AssignNode {
        $nearToken = $lexer->current();
        $this->tokenEater->eat(TokenType::Assign, $lexer);

        return new AssignNode($nearToken, $identifier, $exprParser->parse($lexer, BindingPowerSet::MIN));
    }
}
