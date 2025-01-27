<?php

declare(strict_types=1);

namespace Elfennol\MonkeyPhp\Parser\PrattParser\SubParser;

use Elfennol\MonkeyPhp\Lexer\LexerInterface;
use Elfennol\MonkeyPhp\Node\CallableInterface;
use Elfennol\MonkeyPhp\Node\Catalog\Expr\FnCallNode;
use Elfennol\MonkeyPhp\Node\ExprNodeInterface;
use Elfennol\MonkeyPhp\Parser\Eater\TokenEater;
use Elfennol\MonkeyPhp\Parser\ExprParserInterface;
use Elfennol\MonkeyPhp\Parser\ParserException;
use Elfennol\MonkeyPhp\Parser\ParserExceptionType;
use Elfennol\MonkeyPhp\Parser\PrattParser\BindingPowerSet;
use Elfennol\MonkeyPhp\Token\TokenType;

readonly class InfixFnCallParser implements InfixParserInterface
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
        if (!$left instanceof CallableInterface) {
            throw new ParserException(ParserExceptionType::NodeTypeInvalid, [
                'currentToken' => $lexer->current()->normalize(),
                'leftNode' => $left->debug(),
            ], 'Unable to parse infix FnCall expression. Left node must be callable.');
        }

        $nearToken = $lexer->current();
        $fnParams = $this->parseFnArgs($lexer, $exprParser);

        return new FnCallNode($nearToken, $left, $fnParams);
    }

    /**
     * @return ExprNodeInterface[]
     */
    private function parseFnArgs(LexerInterface $lexer, ExprParserInterface $exprParser): array
    {
        $this->tokenEater->eat(TokenType::Lparen, $lexer);
        $args = [];
        while (TokenType::Rparen !== $lexer->current()->type) {
            $expr = $exprParser->parse($lexer, BindingPowerSet::MIN);
            $args[] = $expr;
            /** @phpstan-ignore notIdentical.alwaysTrue */
            if (TokenType::Rparen !== $lexer->current()->type) {
                $this->tokenEater->eat(TokenType::Comma, $lexer);
            }
        }

        $this->tokenEater->eat(TokenType::Rparen, $lexer);

        return $args;
    }
}
