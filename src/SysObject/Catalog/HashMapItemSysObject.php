<?php

declare(strict_types=1);

namespace Elfennol\MonkeyPhp\SysObject\Catalog;

use Elfennol\MonkeyPhp\SysObject\AtomSysObjectInterface;
use Elfennol\MonkeyPhp\SysObject\SysObjectInterface;
use Elfennol\MonkeyPhp\SysObject\SysObjectType;
use Stringable;

readonly class HashMapItemSysObject implements SysObjectInterface, Stringable
{
    public function __construct(private AtomSysObjectInterface $key, private AtomSysObjectInterface $value)
    {
    }

    public function type(): SysObjectType
    {
        return SysObjectType::HashMapItem;
    }

    public function key(): AtomSysObjectInterface
    {
        return $this->key;
    }

    public function value(): AtomSysObjectInterface
    {
        return $this->value;
    }

    public function __toString(): string
    {
        $key = $this->key->nodeValue();
        $value = $this->value->nodeValue();
        if (SysObjectType::String === $this->key->type()) {
            $key = sprintf('"%s"', $key);
        }
        if (SysObjectType::String === $this->value->type()) {
            $value = sprintf('"%s"', $value);
        }

        return sprintf('%s: %s', $key, $value);
    }
}
