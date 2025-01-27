<?php

declare(strict_types=1);

namespace Elfennol\MonkeyPhp\Parser\PrattParser\SubParser;

use Elfennol\MonkeyPhp\Lexer\LexerInterface;
use Elfennol\MonkeyPhp\Node\Catalog\Expr\IndexExprNode;
use Elfennol\MonkeyPhp\Node\ExprNodeInterface;
use Elfennol\MonkeyPhp\Parser\Eater\TokenEater;
use Elfennol\MonkeyPhp\Parser\ExprParserInterface;
use Elfennol\MonkeyPhp\Parser\PrattParser\BindingPowerSet;
use Elfennol\MonkeyPhp\Token\TokenType;

readonly class InfixIndexExprParser implements InfixParserInterface
{
    public function __construct(private TokenEater $tokenEater)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function parse(
        LexerInterface $lexer,
        int $currentBp,
        ExprNodeInterface $left,
        ExprParserInterface $exprParser
    ): ExprNodeInterface {
        $nearToken = $lexer->current();
        $this->tokenEater->eat(TokenType::Lbracket, $lexer);
        $index = $exprParser->parse($lexer, BindingPowerSet::MIN);
        $this->tokenEater->eat(TokenType::Rbracket, $lexer);

        return new IndexExprNode($nearToken, $left, $index);
    }
}
