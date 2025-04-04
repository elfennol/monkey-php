<?php

declare(strict_types=1);

namespace Elfennol\MonkeyPhp\SysObject\Catalog;

use Elfennol\MonkeyPhp\SysObject\AtomSysObjectInterface;
use Elfennol\MonkeyPhp\SysObject\SysObjectType;
use Elfennol\MonkeyPhp\Utils\Type\HashableTrait;

readonly class StringSysObject implements AtomSysObjectInterface
{
    use HashableTrait;

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

    protected function hashableProperties(): array
    {
        return [$this->nodeValue()];
    }

    public function __toString(): string
    {
        return $this->nodeValue();
    }
}
