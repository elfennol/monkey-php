<?php

declare(strict_types=1);

namespace Elfennol\MonkeyPhp\Evaluator;

enum EvaluatorExceptionType
{
    case FnWrongArgsNumber;
    case FnWrongArgType;
    case WrongIndex;
    case EmptySysObject;

    case ConditionInvalid;
    case OperandInvalid;

    case ContextIdentifierConflict;
    case ContextIdentifierUndefined;
    case ContextIdentifierNotFound;
    case ExprNotCallable;

    case SysObjectInvalid;
    case NodeNotSupported;
    case InfixOpNotSupported;
    case PrefixOpNotSupported;
    case PostfixOpNotSupported;
}
