<?php

declare(strict_types=1);

namespace Elfennol\MonkeyPhp\Coder;

use Elfennol\MonkeyPhp\Utils\Option\None;
use Elfennol\MonkeyPhp\Utils\Option\Option;
use Elfennol\MonkeyPhp\Utils\Option\Some;

readonly class Definitions
{
    /**
     * @var array<int, Definition>
     */
    private array $definitions;

    public function __construct()
    {
        $this->definitions = [
            OpCode::Const->value => new Definition(OpCode::Const, [2]),
            OpCode::Pop->value => new Definition(OpCode::Pop, []),
            OpCode::Unit->value => new Definition(OpCode::Unit, []),
            OpCode::Add->value => new Definition(OpCode::Add, []),
            OpCode::Sub->value => new Definition(OpCode::Sub, []),
            OpCode::Mul->value => new Definition(OpCode::Mul, []),
            OpCode::Div->value => new Definition(OpCode::Div, []),
            OpCode::True->value => new Definition(OpCode::True, []),
            OpCode::False->value => new Definition(OpCode::False, []),
            OpCode::Equal->value => new Definition(OpCode::Equal, []),
            OpCode::NotEqual->value => new Definition(OpCode::NotEqual, []),
            OpCode::GreaterThan->value => new Definition(OpCode::GreaterThan, []),
            OpCode::Minus->value => new Definition(OpCode::Minus, []),
            OpCode::Bang->value => new Definition(OpCode::Bang, []),
            OpCode::Factorial->value => new Definition(OpCode::Factorial, []),
            OpCode::Pow->value => new Definition(OpCode::Pow, []),
            OpCode::JumpNotTruthy->value => new Definition(OpCode::JumpNotTruthy, [2]),
            OpCode::Jump->value => new Definition(OpCode::Jump, [2]),
            OpCode::GetGlobal->value => new Definition(OpCode::GetGlobal, [2]),
            OpCode::SetGlobal->value => new Definition(OpCode::SetGlobal, [2]),
            OpCode::Array->value => new Definition(OpCode::Array, [2]),
            OpCode::HashMap->value => new Definition(OpCode::HashMap, [2]),
            OpCode::Index->value => new Definition(OpCode::Index, []),
            OpCode::Call->value => new Definition(OpCode::Call, [1]),
            OpCode::ReturnValue->value => new Definition(OpCode::ReturnValue, []),
            OpCode::Return->value => new Definition(OpCode::Return, []),
            OpCode::GetLocal->value => new Definition(OpCode::GetLocal, [1]),
            OpCode::SetLocal->value => new Definition(OpCode::SetLocal, [1]),
            OpCode::GetBuiltin->value => new Definition(OpCode::GetBuiltin, [1]),
            OpCode::Closure->value => new Definition(OpCode::Closure, [2, 1]),
            OpCode::GetFree->value => new Definition(OpCode::GetFree, [1]),
            OpCode::CurrentClosure->value => new Definition(OpCode::CurrentClosure, []),
        ];
    }

    /**
     * @return Option<Definition>
     */
    public function lookup(OpCode $opCode): Option
    {
        return empty($this->definitions[$opCode->value]) ? new None() : new Some($this->definitions[$opCode->value]);
    }
}
