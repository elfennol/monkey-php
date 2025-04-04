<?php

declare(strict_types=1);

namespace Elfennol\MonkeyPhp\Compiler\Catalog;

use Elfennol\MonkeyPhp\Coder\OpCode;
use Elfennol\MonkeyPhp\Compiler\AbstractCompilerRule;
use Elfennol\MonkeyPhp\Compiler\CompilerContext;
use Elfennol\MonkeyPhp\Node\Catalog\Expr\FnCallNode;
use Elfennol\MonkeyPhp\Node\NodeInterface;

/**
 * @extends AbstractCompilerRule<FnCallNode>
 */
readonly class FnCallCompilerRule extends AbstractCompilerRule
{
    public function execute(CompilerContext $context, NodeInterface $node): void
    {
        foreach ($node->fnArgs() as $arg) {
            $context->process($arg);
        }

        $context->process($node->fnExpr());
        $this->emit($context, OpCode::Call, [count($node->fnArgs())]);
    }

    protected function support(NodeInterface $node): bool
    {
        return $node instanceof FnCallNode;
    }
}
