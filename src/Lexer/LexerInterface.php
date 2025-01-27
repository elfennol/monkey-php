<?php

declare(strict_types=1);

namespace Elfennol\MonkeyPhp\Lexer;

use Elfennol\MonkeyPhp\Token\Token;
use Iterator;

/**
 * @extends Iterator<int, Token>
 */
interface LexerInterface extends Iterator
{
}
