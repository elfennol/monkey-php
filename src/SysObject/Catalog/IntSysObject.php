<?php

declare(strict_types=1);

namespace Elfennol\MonkeyPhp\SysObject\Catalog;

use Elfennol\MonkeyPhp\SysObject\AtomSysObjectInterface;
use Elfennol\MonkeyPhp\SysObject\InvalidSysObjectException;
use Elfennol\MonkeyPhp\SysObject\SysObjectType;

readonly class IntSysObject implements AtomSysObjectInterface
{
    private string $value;

    public function __construct(string $value)
    {
        if (1 !== preg_match('/^-*[0-9]+$/', $value)) {
            throw new InvalidSysObjectException('Must be an integer.');
        }

        $this->value = $value;
    }

    public function type(): SysObjectType
    {
        return SysObjectType::Int;
    }

    public function nodeValue(): string
    {
        return $this->value;
    }

    public function nativeValue(): int
    {
        return (int)$this->value;
    }

    public function isPositive(): bool
    {
        return (int)$this->nodeValue() >= 0;
    }
}
