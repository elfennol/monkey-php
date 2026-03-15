<?php

declare(strict_types=1);

namespace Elfennol\MonkeyPhp\Lexer;

use Elfennol\MonkeyPhp\Utils\Exception\ContextException;
use Elfennol\MonkeyPhp\Utils\Exception\ContextExceptionInterface;

/**
 * @extends ContextException<LexerExceptionType>
 */
class LexerException extends ContextException implements ContextExceptionInterface
{
}
