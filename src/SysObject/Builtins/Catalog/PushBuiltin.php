<?php

declare(strict_types=1);

namespace Elfennol\MonkeyPhp\SysObject\Builtins\Catalog;

use Elfennol\MonkeyPhp\SysObject\Builtins\BuiltinInterface;
use Elfennol\MonkeyPhp\SysObject\Builtins\BuiltinName;
use Elfennol\MonkeyPhp\SysObject\Builtins\Validator;
use Elfennol\MonkeyPhp\SysObject\Catalog\ArraySysObject;
use Elfennol\MonkeyPhp\SysObject\Catalog\IntSysObject;
use Elfennol\MonkeyPhp\SysObject\SysObjectInterface;

readonly class PushBuiltin implements BuiltinInterface
{
    public function __construct(private Validator $validator)
    {
    }

    public function exec(SysObjectInterface ...$args): SysObjectInterface
    {
        $this->validator->assertArgsNumber(BuiltinName::Push, $args, 2);
        $this->validator->assertArgsType(BuiltinName::Push, $args[0], ArraySysObject::class);
        $this->validator->assertArgsType(BuiltinName::Push, $args[1], IntSysObject::class);
        /** @var ArraySysObject $array */
        $array = $args[0];
        $elements = $array->elements();
        /** @var IntSysObject $value */
        $value = $args[1];
        $elements[] = $value;

        return new ArraySysObject($elements);
    }
}
