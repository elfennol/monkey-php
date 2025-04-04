<?php

declare(strict_types=1);

namespace Elfennol\MonkeyPhp\SysObject\Builtins\Catalog;

use Elfennol\MonkeyPhp\SysObject\AtomSysObjectInterface;
use Elfennol\MonkeyPhp\SysObject\Builtins\BuiltinInterface;
use Elfennol\MonkeyPhp\SysObject\Builtins\BuiltinName;
use Elfennol\MonkeyPhp\SysObject\Builtins\Validator;
use Elfennol\MonkeyPhp\SysObject\Catalog\UnitSysObject;
use Elfennol\MonkeyPhp\SysObject\SysObjectInterface;

readonly class EchoBuiltin implements BuiltinInterface
{
    public function __construct(private Validator $validator)
    {
    }

    public function exec(SysObjectInterface ...$args): SysObjectInterface
    {
        $this->validator->assertArgsNumber(BuiltinName::Echo, $args, 1);
        $this->validator->assertArgsType(BuiltinName::Echo, $args[0], AtomSysObjectInterface::class);
        /** @var AtomSysObjectInterface $arg */
        $arg = $args[0];

        echo $arg->nodeValue();

        return new UnitSysObject();
    }
}
