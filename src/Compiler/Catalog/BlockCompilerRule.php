<?php

declare(strict_types=1);

namespace Elfennol\MonkeyPhp\Compiler\Catalog;

use Elfennol\MonkeyPhp\Coder\OpCode;
use Elfennol\MonkeyPhp\Compiler\AbstractCompilerRule;
use Elfennol\MonkeyPhp\Compiler\CompilerContext;
use Elfennol\MonkeyPhp\Node\Catalog\BlockNode;
use Elfennol\MonkeyPhp\Node\ExprNodeInterface;
use Elfennol\MonkeyPhp\Node\NodeInterface;

/**
 * @extends AbstractCompilerRule<BlockNode>
 */
readonly class BlockCompilerRule extends AbstractCompilerRule
{
    public function execute(CompilerContext $context, NodeInterface $node): void
    {
        $totalStmt = count($node->stmts());
        foreach ($node->stmts() as $stmtIndex => $stmt) {
            $context->process($stmt);
            if ($stmtIndex < $totalStmt - 1 && $stmt instanceof ExprNodeInterface) {
                $this->emit($context, OpCode::Pop);
            }
        }
    }

    protected function support(NodeInterface $node): bool
    {
        return $node instanceof BlockNode;
    }
}
