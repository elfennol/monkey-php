<?php

declare(strict_types=1);

namespace Elfennol\MonkeyPhp\Node\Catalog\Atom;

use Elfennol\MonkeyPhp\Node\NodeType;

readonly class BoolNode extends AtomNode
{
    public function type(): NodeType
    {
        return NodeType::Bool;
    }
}
