<?php

declare(strict_types=1);

namespace Elfennol\MonkeyPhp\Vm;

use Elfennol\MonkeyPhp\Utils\Exception\ContextException;
use Elfennol\MonkeyPhp\Utils\Exception\ContextExceptionInterface;

/**
 * @extends ContextException<VmExceptionType>
 */
class VmException extends ContextException implements ContextExceptionInterface
{
}
