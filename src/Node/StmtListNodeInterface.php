<?php

declare(strict_types=1);

namespace Elfennol\MonkeyPhp\Node;

interface StmtListNodeInterface extends NodeInterface
{
    /**
     * @return StmtNodeInterface[]
     */
    public function stmts(): array;
}
