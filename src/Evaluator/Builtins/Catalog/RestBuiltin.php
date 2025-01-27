<?php

declare(strict_types=1);

namespace Elfennol\MonkeyPhp\Evaluator\Builtins\Catalog;

use Elfennol\MonkeyPhp\Evaluator\BuiltinInterface;
use Elfennol\MonkeyPhp\Evaluator\Builtins\BuiltinName;
use Elfennol\MonkeyPhp\Evaluator\Builtins\Validator;
use Elfennol\MonkeyPhp\SysObject\Catalog\ArraySysObject;
use Elfennol\MonkeyPhp\SysObject\SysObjectInterface;

readonly class RestBuiltin implements BuiltinInterface
{
    public function __construct(private Validator $validator)
    {
    }

    public function exec(SysObjectInterface ...$args): SysObjectInterface
    {
        $this->validator->assertArgsNumber(BuiltinName::Rest, $args, 1);
        $this->validator->assertArgsType(BuiltinName::Rest, $args[0], ArraySysObject::class);
        /** @var ArraySysObject $arg */
        $arg = $args[0];

        if (in_array(count($arg->elements()), [0, 1], true)) {
            return new ArraySysObject([]);
        }

        return new ArraySysObject(array_slice($arg->elements(), 1));
    }
}
