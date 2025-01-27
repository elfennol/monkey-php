<?php

declare(strict_types=1);

namespace Elfennol\MonkeyPhp\Utils\Char;

enum Char: string
{
    case Asterisk = '*';
    case Backslash = '\\';
    case Colon = ':';
    case Comma = ',';
    case CR = "\r";
    case DoubleQuotes = '"';
    case Equals = '=';
    case ExclamationMark = '!';
    case GreaterThan = '>';
    case HT = "\t";
    case LeftBrace = '{';
    case LeftBracket = '[';
    case LeftParen = '(';
    case LessThan = '<';
    case LF = "\n";
    case Minus = '-';
    case Nul = "\0";
    case Plus = '+';
    case RightBrace = '}';
    case RightBracket = ']';
    case RightParen = ')';
    case Semicolon = ';';
    case Slash = '/';
    case SPACE = ' ';
    case VT = "\v";
}
