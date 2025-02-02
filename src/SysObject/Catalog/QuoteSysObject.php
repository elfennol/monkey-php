<?php

declare(strict_types=1);

namespace Elfennol\MonkeyPhp\SysObject\Catalog;

use Elfennol\MonkeyPhp\Node\NodeInterface;
use Elfennol\MonkeyPhp\SysObject\MacroSysObjectInterface;
use Elfennol\MonkeyPhp\SysObject\SysObjectType;
use Stringable;

readonly class QuoteSysObject implements MacroSysObjectInterface, Stringable
{
    public function __construct(private NodeInterface $node)
    {
    }

    public function type(): SysObjectType
    {
        return SysObjectType::Quote;
    }

    public function node(): NodeInterface
    {
        return $this->node;
    }

    public function __toString(): string
    {
        return json_encode($this->node, JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT);
    }
}
