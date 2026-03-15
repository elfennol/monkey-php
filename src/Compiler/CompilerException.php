<?php

declare(strict_types=1);

namespace Elfennol\MonkeyPhp\Compiler;

use Elfennol\MonkeyPhp\Utils\Exception\ContextException;
use Elfennol\MonkeyPhp\Utils\Exception\ContextExceptionInterface;

/**
 * @extends ContextException<CompilerExceptionType>
 */
class CompilerException extends ContextException implements ContextExceptionInterface
{
}
