<?php

declare(strict_types=1);

namespace Elfennol\MonkeyPhp\Lexer;

use Elfennol\MonkeyPhp\Token\TokenType;
use Elfennol\MonkeyPhp\Utils\Option\None;
use Elfennol\MonkeyPhp\Utils\Option\Option;
use Elfennol\MonkeyPhp\Utils\Option\Some;

readonly class TokenTypeFinder
{
    /**
     * @return Option<TokenType>
     *
     * @SuppressWarnings(PHPMD.StaticAccess) PHP built-in function TokenType::tryFrom.
     */
    public function findBySpecialString(string $value): Option
    {
        $tokenType = TokenType::tryFrom($value);

        if (!$tokenType instanceof TokenType || !in_array($tokenType, TokenType::specialStringCases())) {
            return new None();
        }

        return new Some($tokenType);
    }

    /**
     * @return Option<TokenType>
     *
     * @SuppressWarnings(PHPMD.StaticAccess) PHP built-in function TokenType::tryFrom.
     */
    public function findByKeywords(string $value): Option
    {
        $tokenType = TokenType::tryFrom($value);

        if (!$tokenType instanceof TokenType || !in_array($tokenType, TokenType::keywordCases())) {
            return new None();
        }

        return new Some($tokenType);
    }
}
