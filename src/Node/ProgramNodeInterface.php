<?php

declare(strict_types=1);

namespace Elfennol\MonkeyPhp\Node;

use Elfennol\MonkeyPhp\Node\Catalog\ProgramNode;

interface ProgramNodeInterface extends StmtListNodeInterface
{
    /**
     * @param StmtNodeInterface[] $stmts
     */
    public function buildWith(array $stmts): ProgramNode;
}
