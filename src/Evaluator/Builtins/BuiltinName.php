<?php

declare(strict_types=1);

namespace Elfennol\MonkeyPhp\Evaluator\Builtins;

/**
 * @internal
 */
enum BuiltinName: string
{
    case Echo = 'echo';
    case First = 'first';
    case Last = 'last';
    case Len = 'len';
    case Push = 'push';
    case Rest = 'rest';
}
