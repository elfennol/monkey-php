<?php

declare(strict_types=1);

namespace Elfennol\MonkeyPhp\Token;

use JsonSerializable;

enum TokenType: string implements JsonSerializable
{
    //// General ////
    case Illegal = '__illegal__';
    case Eof = "\0";

    //// Identifier ////
    case Identifier = '__identifier__';

    //// Keyword (see keywordCases) ////
    case Function = 'fn';
    case Let = 'let';
    case True = 'true';
    case False = 'false';
    case If = 'if';
    case Else = 'else';
    case Return = 'return';
    case Macro = 'macro';

    //// Literal ////
    case Int = '__int__';
    case String = '__string__';

    //// Special string (see specialStringCases) ////
    // Operator //
    case Assign = '=';
    case Plus = '+';
    case Minus = '-';
    case Asterisk = '*';
    case Slash = '/';
    case DoubleAsterisk = '**';
    case Bang = '!';
    case Lt = '<';
    case Gt = '>';
    case Eq = '==';
    case NotEq = '!=';
    // Separator //
    case Lparen = '(';
    case Rparen = ')';
    case Lbrace = '{';
    case Rbrace = '}';
    case Lbracket = '[';
    case Rbracket = ']';
    case Comma = ',';
    case Semicolon = ';';
    case Colon = ':';

    /**
     * @return TokenType[]
     */
    public static function keywordCases(): array
    {
        return [
            self::Function,
            self::Let,
            self::True,
            self::False,
            self::If,
            self::Else,
            self::Return,
            self::Macro,
        ];
    }

    /**
     * @return TokenType[]
     */
    public static function specialStringCases(): array
    {
        return [
            self::Assign,
            self::Plus,
            self::Minus,
            self::Asterisk,
            self::Slash,
            self::DoubleAsterisk,
            self::Bang,
            self::Lt,
            self::Gt,
            self::Eq,
            self::NotEq,
            self::Lparen,
            self::Rparen,
            self::Lbrace,
            self::Rbrace,
            self::Lbracket,
            self::Rbracket,
            self::Comma,
            self::Semicolon,
            self::Colon,
        ];
    }

    /**
     * @return array{name: string}
     */
    public function jsonSerialize(): array
    {
        return [
            'name' => $this->name,
        ];
    }
}
