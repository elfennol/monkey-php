<?php

declare(strict_types=1);

namespace Elfennol\MonkeyPhp\Compiler;

enum CompilerExceptionType
{
    case ConstantNotSupported;
    case NodeNotSupported;
    case InvalidOpCode;
    case InstructionOverflow;
    case SymbolNotFound;
    case UnexpectedScope;
}
