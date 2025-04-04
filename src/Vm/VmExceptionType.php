<?php

declare(strict_types=1);

namespace Elfennol\MonkeyPhp\Vm;

enum VmExceptionType
{
    case StackOverflow;
    case StackUnderflow;
    case GlobalOverflow;
    case GlobalUnknown;
    case GlobalIndexInvalid;
    case InvalidOpCode;
    case InvalidSysObject;
    case InstructionOverflow;
    case InvalidConstantPointer;
    case OperandInvalid;
    case UndefinedArrayKey;
    case CallStackOverflow;
    case CallStackUnderflow;
    case UninitializedLocal;
    case BuiltinCallFailed;
}
