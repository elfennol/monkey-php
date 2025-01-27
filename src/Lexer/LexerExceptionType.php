<?php

declare(strict_types=1);

namespace Elfennol\MonkeyPhp\Lexer;

/**
 * @internal
 */
enum LexerExceptionType
{
    case StringMustBeEnclosed;
    case UnableToBuildTheToken;
}
