<?php

declare(strict_types=1);

namespace Elfennol\MonkeyPhp\Coder;

enum CoderExceptionType
{
    case OpCodeInvalid;
    case OperandInvalid;
}
