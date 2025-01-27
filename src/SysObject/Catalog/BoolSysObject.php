<?php

declare(strict_types=1);

namespace Elfennol\MonkeyPhp\SysObject\Catalog;

use Elfennol\MonkeyPhp\SysObject\AtomSysObjectInterface;
use Elfennol\MonkeyPhp\SysObject\InvalidSysObjectException;
use Elfennol\MonkeyPhp\SysObject\SysObjectType;
use Elfennol\MonkeyPhp\Token\TokenType;

readonly class BoolSysObject implements AtomSysObjectInterface
{
    private string $value;

    public function __construct(string $value)
    {
        if (!in_array($value, [TokenType::True->value, TokenType::False->value], true)) {
            throw new InvalidSysObjectException('Must be a boolean.');
        }

        $this->value = $value;
    }

    public function type(): SysObjectType
    {
        return SysObjectType::Bool;
    }

    public function nodeValue(): string
    {
        return $this->value;
    }

    public function nativeValue(): bool
    {
        return TokenType::True->value === $this->value;
    }
}
