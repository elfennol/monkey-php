<?php

declare(strict_types=1);

namespace Elfennol\MonkeyPhp\Coder;

enum OpCode: int
{
    case Const = 0;
    case Pop = 1;
    case Unit = 2;

    case Add = 10;
    case Sub = 11;
    case Mul = 12;
    case Div = 13;

    case True = 20;
    case False = 21;
    case Equal = 22;
    case NotEqual = 23;
    case GreaterThan = 24;

    case Minus = 30;
    case Bang = 31;

    case Factorial = 40;
    case Pow = 41;

    case JumpNotTruthy = 50;
    case Jump = 51;
    case GetGlobal = 52;
    case SetGlobal = 53;
    case GetLocal = 54;
    case SetLocal = 55;
    case GetBuiltin = 56;

    case Array = 60;
    case HashMap = 61;
    case Index = 62;

    case Call = 70;
    case ReturnValue = 71;
    case Return = 72;

    case Closure = 80;
    case GetFree = 81;
    case CurrentClosure = 82;
}
