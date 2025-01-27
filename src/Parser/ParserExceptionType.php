<?php

declare(strict_types=1);

namespace Elfennol\MonkeyPhp\Parser;

enum ParserExceptionType
{
    case NodeTypeInvalid;
    case PrefixFnUndefined;

    case TokenMustBeAtom;
    case TokenMustBePrefix;
    case TokenMustHaveBindingPower;

    case TokenTypeInvalid;
}
