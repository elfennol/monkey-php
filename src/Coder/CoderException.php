<?php

declare(strict_types=1);

namespace Elfennol\MonkeyPhp\Coder;

use Elfennol\MonkeyPhp\Utils\Exception\ContextException;
use Elfennol\MonkeyPhp\Utils\Exception\ContextExceptionInterface;

/**
 * @extends ContextException<CoderExceptionType>
 */
class CoderException extends ContextException implements ContextExceptionInterface
{
}
