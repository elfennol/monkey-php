<?php

declare(strict_types=1);

namespace Elfennol\MonkeyPhp\SysObject;

use JsonSerializable;

enum SysObjectType implements JsonSerializable
{
    case Bool;
    case Int;
    case String;

    case Array;
    case HashMap;
    case HashMapItem;

    case Builtin;
    case Function;
    case Quote;
    case MacroBuiltin;
    case Macro;

    case Return;
    case Unit;

    /**
     * @return array{name: string}
     */
    public function jsonSerialize(): array
    {
        return [
            'name' => $this->name,
        ];
    }
}
