<?php

declare(strict_types=1);

namespace Elfennol\MonkeyPhp\SysObject\Builtins;

use Elfennol\MonkeyPhp\Utils\Exception\ContextException;
use Elfennol\MonkeyPhp\Utils\Exception\ContextExceptionInterface;

/**
 * @extends ContextException<BuiltinExceptionType>
 */
class BuiltinException extends ContextException implements ContextExceptionInterface
{
}
