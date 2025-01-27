<?php

declare(strict_types=1);

namespace Elfennol\MonkeyPhp\Lexer;

use Elfennol\MonkeyPhp\Token\Token;
use Elfennol\MonkeyPhp\Token\TokenType;

readonly class TokenBuilder
{
    public function __construct(private TokenTypeFinder $tokenTypeFinder)
    {
    }

    public function makeSpecialString(string $value, int $line, int $col): Token
    {
        $tokenTypeFound = $this->tokenTypeFinder->findBySpecialString($value);

        if ($tokenTypeFound->isNone()) {
            throw new LexerException(
                LexerExceptionType::UnableToBuildTheToken,
                ['value' => $value, 'line' => $line, 'col' => $col]
            );
        }

        return new Token($tokenTypeFound->unwrap(), $value, $line, $col);
    }

    public function makeKeywordOrIdentifier(string $value, int $line, int $col): Token
    {
        $tokenTypeFound = $this->tokenTypeFinder->findByKeywords($value);

        if ($tokenTypeFound->isNone()) {
            return new Token(TokenType::Identifier, $value, $line, $col);
        }

        return new Token($tokenTypeFound->unwrap(), $value, $line, $col);
    }

    public function makeFromType(TokenType $tokenType, string $value, int $line, int $col): Token
    {
        return new Token($tokenType, $value, $line, $col);
    }
}
