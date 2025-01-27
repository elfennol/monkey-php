<?php

declare(strict_types=1);

namespace Elfennol\MonkeyPhp\Parser\Eater;

use Elfennol\MonkeyPhp\Lexer\LexerInterface;
use Elfennol\MonkeyPhp\Parser\ParserException;
use Elfennol\MonkeyPhp\Parser\ParserExceptionType;
use Elfennol\MonkeyPhp\Token\TokenType;

readonly class TokenEater
{
    public function eat(TokenType $tokenType, LexerInterface $lexer): void
    {
        if ($lexer->current()->type !== $tokenType) {
            throw new ParserException(ParserExceptionType::TokenTypeInvalid, [
                'currentToken' => $lexer->current()->normalize(),
                'expectedTokenType' => $tokenType,
            ], 'The eater has indigestion.');
        }

        $lexer->next();
    }
}
