<?php

declare(strict_types=1);

namespace Elfennol\MonkeyPhp\Compiler\Catalog;

use Elfennol\MonkeyPhp\Coder\OpCode;
use Elfennol\MonkeyPhp\Compiler\AbstractCompilerRule;
use Elfennol\MonkeyPhp\Compiler\CompilerContext;
use Elfennol\MonkeyPhp\Node\Catalog\Expr\IndexExprNode;
use Elfennol\MonkeyPhp\Node\NodeInterface;

/**
 * @extends AbstractCompilerRule<IndexExprNode>
 */
readonly class IndexCompilerRule extends AbstractCompilerRule
{
    public function execute(CompilerContext $context, NodeInterface $node): void
    {
        $context->process($node->left());
        $context->process($node->index());
        $this->emit($context, OpCode::Index);
    }

    protected function support(NodeInterface $node): bool
    {
        return $node instanceof IndexExprNode;
    }
}
