<?php

declare(strict_types=1);

namespace Elfennol\MonkeyPhp\SysObject\Builtins;

use Elfennol\MonkeyPhp\SysObject\SysObjectInterface;

readonly class Validator
{
    /**
     * @param SysObjectInterface[] $args
     */
    public function assertArgsNumber(BuiltinName $name, array $args, int $argsNb): void
    {
        if ($argsNb !== count($args)) {
            throw new BuiltinException(
                BuiltinExceptionType::FnWrongArgsNumber,
                ['name' => $name->value, 'args' => $args]
            );
        }
    }

    public function assertArgsType(BuiltinName $name, SysObjectInterface $arg, string $sysObjectClass): void
    {
        if (!$arg instanceof $sysObjectClass) {
            $this->throwFnWrongArgType($name, $arg);
        }
    }

    public function throwFnWrongArgType(BuiltinName $name, SysObjectInterface $arg): never
    {
        throw new BuiltinException(
            BuiltinExceptionType::FnWrongArgType,
            ['name' => $name->value, 'argType' => $arg->type()]
        );
    }
}
