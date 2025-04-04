<?php

declare(strict_types=1);

namespace Elfennol\MonkeyPhp\Compiler\Symbol;

enum Scope
{
    case Global;
    case Local;
    case Builtin;
    case Free;
    case Function;
}
