<?php

declare(strict_types=1);

namespace Elfennol\MonkeyPhp\SysObject\Builtins\Catalog;

use Elfennol\MonkeyPhp\SysObject\AtomSysObjectInterface;
use Elfennol\MonkeyPhp\SysObject\Builtins\BuiltinException;
use Elfennol\MonkeyPhp\SysObject\Builtins\BuiltinExceptionType;
use Elfennol\MonkeyPhp\SysObject\Builtins\BuiltinInterface;
use Elfennol\MonkeyPhp\SysObject\Builtins\BuiltinName;
use Elfennol\MonkeyPhp\SysObject\Builtins\Validator;
use Elfennol\MonkeyPhp\SysObject\Catalog\ArraySysObject;
use Elfennol\MonkeyPhp\SysObject\SysObjectInterface;

readonly class FirstBuiltin implements BuiltinInterface
{
    public function __construct(private Validator $validator)
    {
    }

    public function exec(SysObjectInterface ...$args): AtomSysObjectInterface
    {
        $this->validator->assertArgsNumber(BuiltinName::First, $args, 1);
        $this->validator->assertArgsType(BuiltinName::First, $args[0], ArraySysObject::class);
        /** @var ArraySysObject $arg */
        $arg = $args[0];

        if (0 === count($arg->elements())) {
            throw new BuiltinException(
                BuiltinExceptionType::EmptySysObject,
                ['name' => BuiltinName::First->value, 'args' => $args]
            );
        }

        return $arg->elements()[0];
    }
}
