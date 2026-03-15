<?php

declare(strict_types=1);

namespace Elfennol\MonkeyPhp\SysObject\Builtins;

enum BuiltinExceptionType
{
    case FnWrongArgsNumber;
    case FnWrongArgType;
    case EmptySysObject;
}
