<?php

declare(strict_types=1);

namespace Elfennol\MonkeyPhp\SysObject\Catalog;

use Elfennol\MonkeyPhp\SysObject\AtomSysObjectInterface;
use Elfennol\MonkeyPhp\SysObject\SysObjectType;

readonly class StringSysObject implements AtomSysObjectInterface
{
    public function __construct(private string $value)
    {
    }

    public function type(): SysObjectType
    {
        return SysObjectType::String;
    }

    public function nodeValue(): string
    {
        return $this->value;
    }

    public function nativeValue(): string
    {
        return $this->value;
    }
}
