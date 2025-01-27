<?php

declare(strict_types=1);

namespace Elfennol\MonkeyPhp\Evaluator\Builtins\Catalog;

use Elfennol\MonkeyPhp\Evaluator\BuiltinInterface;
use Elfennol\MonkeyPhp\Evaluator\Builtins\BuiltinName;
use Elfennol\MonkeyPhp\Evaluator\Builtins\Validator;
use Elfennol\MonkeyPhp\SysObject\Catalog\ArraySysObject;
use Elfennol\MonkeyPhp\SysObject\Catalog\IntSysObject;
use Elfennol\MonkeyPhp\SysObject\Catalog\StringSysObject;
use Elfennol\MonkeyPhp\SysObject\SysObjectInterface;

readonly class LenBuiltin implements BuiltinInterface
{
    public function __construct(private Validator $validator)
    {
    }

    public function exec(SysObjectInterface ...$args): SysObjectInterface
    {
        $this->validator->assertArgsNumber(BuiltinName::Len, $args, 1);

        return match (true) {
            $args[0] instanceof StringSysObject => new IntSysObject((string)strlen($args[0]->nativeValue())),
            $args[0] instanceof ArraySysObject => new IntSysObject((string)count($args[0]->elements())),
            default => $this->validator->throwFnWrongArgType(BuiltinName::Len, $args[0]),
        };
    }
}
