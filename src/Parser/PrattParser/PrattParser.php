<?php

declare(strict_types=1);

namespace Elfennol\MonkeyPhp\Parser\PrattParser;

use Elfennol\MonkeyPhp\Lexer\LexerInterface;
use Elfennol\MonkeyPhp\Node\ExprNodeInterface;
use Elfennol\MonkeyPhp\Parser\ExprParserInterface;
use Elfennol\MonkeyPhp\Parser\ParserException;
use Elfennol\MonkeyPhp\Parser\ParserExceptionType;
use Elfennol\MonkeyPhp\Token\TokenType;
use Elfennol\MonkeyPhp\Utils\Option\None;
use Elfennol\MonkeyPhp\Utils\Option\Option;
use Elfennol\MonkeyPhp\Utils\Option\Some;

readonly class PrattParser implements ExprParserInterface
{
    public function __construct(
        private BindingPowerSet $bindingPowerSet,
        private ParserFn $parserFn,
    ) {
    }

    public function parse(LexerInterface $lexer, int $startingBp): ExprNodeInterface
    {
        $prefixFn = $this->parserFn->getPrefixFn(($lexer->current()->type));

        if ($prefixFn->isNone()) {
            throw new ParserException(ParserExceptionType::TokenMustBePrefix, [
                'currentToken' => $lexer->current()->normalize(),
            ], 'Unable to parse prefix expression.');
        }

        $left = $prefixFn->unwrap()->parse($lexer, $this->prefixBp($lexer), $this);

        while (!in_array($lexer->current()->type, [TokenType::Eof, TokenType::Semicolon], true)) {
            $postfixFn = $this->parserFn->getPostfixFn($lexer->current()->type);
            if ($postfixFn->isSome()) {
                $betterBp = $this->betterPostfixBp($lexer, $startingBp);
                if ($betterBp->isNone()) {
                    break;
                }

                $left = $postfixFn->unwrap()->parse($lexer, $left);

                continue;
            }

            $infixFn = $this->parserFn->getInfixFn($lexer->current()->type);
            if ($infixFn->isSome()) {
                $betterBp = $this->betterInfixBp($lexer, $startingBp);

                if ($betterBp->isNone()) {
                    break;
                }

                $left = $infixFn->unwrap()->parse($lexer, $betterBp->unwrap(), $left, $this);

                continue;
            }

            break;
        }

        return $left;
    }

    private function prefixBp(LexerInterface $lexer): int
    {
        $currentBp = $this->bindingPowerSet->getPrefixBindingPower($lexer->current()->type);
        if ($currentBp->isNone()) {
            throw new ParserException(ParserExceptionType::TokenMustHaveBindingPower, [
                'currentToken' => $lexer->current()->normalize(),
            ], 'Unable to parse prefix expression.');
        }

        return $currentBp->unwrap()[0];
    }

    /**
     * @return Option<int>
     */
    private function betterInfixBp(LexerInterface $lexer, int $startingBp): Option
    {
        $currentBp = $this->bindingPowerSet->getInfixBindingPower($lexer->current()->type);
        if ($currentBp->isNone()) {
            throw new ParserException(ParserExceptionType::TokenMustHaveBindingPower, [
                'currentToken' => $lexer->current()->normalize(),
            ], 'Unable to parse infix expression.');
        }

        [$currentBpValueLeft, $currentBpValueRight] = $currentBp->unwrap();

        if ($currentBpValueLeft < $startingBp) {
            return new None();
        }

        return new Some($currentBpValueRight);
    }

    /**
     * @return Option<int>
     */
    private function betterPostfixBp(LexerInterface $lexer, int $startingBp): Option
    {
        $currentBp = $this->bindingPowerSet->getPostfixBindingPower($lexer->current()->type);
        if ($currentBp->isNone()) {
            throw new ParserException(ParserExceptionType::TokenMustHaveBindingPower, [
                'currentToken' => $lexer->current()->normalize(),
            ], 'Unable to parse postfix expression.');
        }

        [$currentBpValue] = $currentBp->unwrap();

        if ($currentBpValue < $startingBp) {
            return new None();
        }

        return new Some($currentBpValue);
    }
}
