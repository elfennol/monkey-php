<?php

declare(strict_types=1);

namespace Elfennol\MonkeyPhp\Parser\PrattParser\SubParser;

use Elfennol\MonkeyPhp\Lexer\LexerInterface;
use Elfennol\MonkeyPhp\Node\ExprNodeInterface;
use Elfennol\MonkeyPhp\Parser\Eater\TokenEater;
use Elfennol\MonkeyPhp\Parser\ExprParserInterface;
use Elfennol\MonkeyPhp\Token\TokenType;

readonly class PrefixGroupedParser implements PrefixParserInterface
{
    public function __construct(private TokenEater $tokenEater)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function parse(LexerInterface $lexer, int $rightBp, ExprParserInterface $exprParser): ExprNodeInterface
    {
        $this->tokenEater->eat(TokenType::Lparen, $lexer);
        $left = $exprParser->parse($lexer, 0);
        $this->tokenEater->eat(TokenType::Rparen, $lexer);

        return $left;
    }
}
