<?php

declare(strict_types=1);

namespace Elfennol\MonkeyPhp\SysObject\Catalog;

use Elfennol\MonkeyPhp\SysObject\SysObjectInterface;
use Elfennol\MonkeyPhp\SysObject\SysObjectType;
use Stringable;

readonly class HashMapSysObject implements SysObjectInterface, Stringable
{
    /**
     * @param array<string, HashMapItemSysObject> $elements
     */
    public function __construct(private array $elements)
    {
    }

    public function type(): SysObjectType
    {
        return SysObjectType::HashMap;
    }

    /**
     * @return array<string, HashMapItemSysObject>
     */
    public function elements(): array
    {
        return $this->elements;
    }

    public function __toString(): string
    {
        return sprintf('{%s}', implode(', ', $this->elements));
    }
}
