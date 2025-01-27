<?php

declare(strict_types=1);

namespace Elfennol\MonkeyPhp\Parser\PrattParser\SubParser;

use Elfennol\MonkeyPhp\Lexer\LexerInterface;
use Elfennol\MonkeyPhp\Node\Catalog\Expr\ArrayNode;
use Elfennol\MonkeyPhp\Node\ExprNodeInterface;
use Elfennol\MonkeyPhp\Parser\Eater\TokenEater;
use Elfennol\MonkeyPhp\Parser\ExprParserInterface;
use Elfennol\MonkeyPhp\Parser\PrattParser\BindingPowerSet;
use Elfennol\MonkeyPhp\Token\TokenType;

readonly class PrefixArrayParser implements PrefixParserInterface
{
    public function __construct(private TokenEater $tokenEater)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function parse(LexerInterface $lexer, int $rightBp, ExprParserInterface $exprParser): ExprNodeInterface
    {
        return new ArrayNode($lexer->current(), $this->parseElements($lexer, $exprParser));
    }

    /**
     * @return ExprNodeInterface[]
     */
    private function parseElements(LexerInterface $lexer, ExprParserInterface $exprParser): array
    {
        $this->tokenEater->eat(TokenType::Lbracket, $lexer);

        $elements = [];
        while (TokenType::Rbracket !== $lexer->current()->type) {
            $expr = $exprParser->parse($lexer, BindingPowerSet::MIN);
            $elements[] = $expr;
            /** @phpstan-ignore notIdentical.alwaysTrue */
            if (TokenType::Rbracket !== $lexer->current()->type) {
                $this->tokenEater->eat(TokenType::Comma, $lexer);
            }
        }

        $this->tokenEater->eat(TokenType::Rbracket, $lexer);

        return $elements;
    }
}
