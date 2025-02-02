<?php

declare(strict_types=1);

namespace Elfennol\MonkeyPhp\Parser\PrattParser;

use Elfennol\MonkeyPhp\Token\TokenType;
use Elfennol\MonkeyPhp\Utils\Option\None;
use Elfennol\MonkeyPhp\Utils\Option\Option;
use Elfennol\MonkeyPhp\Utils\Option\Some;

readonly class BindingPowerSet
{
    public const MIN = 0;

    /** @var array<string, array{0: int}> */
    private const array PREFIX = [
        TokenType::Int->value => [0],
        TokenType::String->value => [0],
        TokenType::Identifier->value => [0],
        TokenType::True->value => [0],
        TokenType::False->value => [0],
        TokenType::If->value => [0],
        TokenType::Function->value => [0],
        TokenType::Plus->value => [1000],
        TokenType::Minus->value => [1000],
        TokenType::Bang->value => [1100],
        TokenType::Lbrace->value => [4000],
        TokenType::Lparen->value => [4000],
        TokenType::Lbracket->value => [4000],
        TokenType::Macro->value => [4000],
    ];

    // Left associativity: left binding power < right binding power.
    // Convention: left binding power + 1 = right binding power
    /** @var array<string, array{0: int, 1: int}> */
    private const array INFIX_LEFT = [
        TokenType::Plus->value => [400, 401],
        TokenType::Minus->value => [400, 401],
        TokenType::Asterisk->value => [500, 501],
        TokenType::Slash->value => [500, 501],
        TokenType::Gt->value => [300, 301],
        TokenType::Lt->value => [300, 301],
        TokenType::Eq->value => [200, 201],
        TokenType::NotEq->value => [200, 201],
        TokenType::Lparen->value => [4000, 4001],
        TokenType::Lbracket->value => [5000, 5001],
        TokenType::Assign->value => [0, 1],
    ];

    // Right associativity : left binding power > right binding power.
    // Convention: left binding power = right binding power + 1
    /** @var array<string, array{0: int, 1: int}> */
    private const array INFIX_RIGHT = [
        TokenType::DoubleAsterisk->value => [3001, 3000],
    ];

    /** @var array<string, array{0: int, 1: int}> */
    private const array INFIX = [...self::INFIX_LEFT, ...self::INFIX_RIGHT];

    /** @var array<string, array{0: int}> */
    private const array POSTFIX = [
        TokenType::Bang->value => [2000],
    ];

    /**
     * @return Option<array{0: int}>
     */
    public function getPrefixBindingPower(TokenType $tokenType): Option
    {
        if (!$this->isPrefix($tokenType)) {
            return new None();
        }

        return new Some(self::PREFIX[$tokenType->value]);
    }

    private function isPrefix(TokenType $tokenType): bool
    {
        return array_key_exists($tokenType->value, self::PREFIX);
    }

    /**
     * @return Option<array{0: int, 1: int}>
     */
    public function getInfixBindingPower(TokenType $tokenType): Option
    {
        if (!$this->isInfix($tokenType)) {
            return new None();
        }

        return new Some(self::INFIX[$tokenType->value]);
    }

    private function isInfix(TokenType $tokenType): bool
    {
        return array_key_exists($tokenType->value, self::INFIX);
    }

    /**
     * @return Option<array{0: int}>
     */
    public function getPostfixBindingPower(TokenType $tokenType): Option
    {
        if (!$this->isPostfix($tokenType)) {
            return new None();
        }

        return new Some(self::POSTFIX[$tokenType->value]);
    }

    private function isPostfix(TokenType $tokenType): bool
    {
        return array_key_exists($tokenType->value, self::POSTFIX);
    }
}
