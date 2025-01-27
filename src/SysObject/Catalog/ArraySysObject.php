<?php

declare(strict_types=1);

namespace Elfennol\MonkeyPhp\SysObject\Catalog;

use Elfennol\MonkeyPhp\SysObject\AtomSysObjectInterface;
use Elfennol\MonkeyPhp\SysObject\SysObjectInterface;
use Elfennol\MonkeyPhp\SysObject\SysObjectType;
use Stringable;

readonly class ArraySysObject implements SysObjectInterface, Stringable
{
    /**
     * @param AtomSysObjectInterface[] $elements
     */
    public function __construct(private array $elements)
    {
    }

    public function type(): SysObjectType
    {
        return SysObjectType::Array;
    }

    /**
     * @return AtomSysObjectInterface[]
     */
    public function elements(): array
    {
        return $this->elements;
    }

    public function __toString(): string
    {
        return sprintf(
            '[%s]',
            implode(', ', array_map(fn (AtomSysObjectInterface $element) => $element->nodeValue(), $this->elements))
        );
    }
}
