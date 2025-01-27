<?php

declare(strict_types=1);

namespace Elfennol\MonkeyPhp\Parser\PrattParser\SubParser;

use Elfennol\MonkeyPhp\Lexer\LexerInterface;
use Elfennol\MonkeyPhp\Node\Catalog\Expr\HashMapItemNode;
use Elfennol\MonkeyPhp\Node\Catalog\Expr\HashMapNode;
use Elfennol\MonkeyPhp\Node\ExprNodeInterface;
use Elfennol\MonkeyPhp\Parser\Eater\TokenEater;
use Elfennol\MonkeyPhp\Parser\ExprParserInterface;
use Elfennol\MonkeyPhp\Parser\PrattParser\BindingPowerSet;
use Elfennol\MonkeyPhp\Token\TokenType;

readonly class PrefixHashMapParser implements PrefixParserInterface
{
    public function __construct(private TokenEater $tokenEater)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function parse(LexerInterface $lexer, int $rightBp, ExprParserInterface $exprParser): ExprNodeInterface
    {
        return new HashMapNode($lexer->current(), $this->parseElements($lexer, $exprParser));
    }

    /**
     * @return HashMapItemNode[]
     */
    private function parseElements(LexerInterface $lexer, ExprParserInterface $exprParser): array
    {
        $this->tokenEater->eat(TokenType::Lbrace, $lexer);

        $elements = [];
        while (TokenType::Rbrace !== $lexer->current()->type) {
            $key = $exprParser->parse($lexer, BindingPowerSet::MIN);
            $nearToken = $lexer->current();
            $this->tokenEater->eat(TokenType::Colon, $lexer);
            $value = $exprParser->parse($lexer, BindingPowerSet::MIN);
            $elements[] = new HashMapItemNode($nearToken, $key, $value);
            /** @phpstan-ignore notIdentical.alwaysTrue */
            if (TokenType::Rbrace !== $lexer->current()->type) {
                $this->tokenEater->eat(TokenType::Comma, $lexer);
            }
        }

        $this->tokenEater->eat(TokenType::Rbrace, $lexer);

        return $elements;
    }
}
