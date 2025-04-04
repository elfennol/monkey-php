<?php

declare(strict_types=1);

namespace Elfennol\MonkeyPhp\Compiler\Catalog;

use Elfennol\MonkeyPhp\Coder\OpCode;
use Elfennol\MonkeyPhp\Compiler\AbstractCompilerRule;
use Elfennol\MonkeyPhp\Compiler\CompilerContext;
use Elfennol\MonkeyPhp\Node\Catalog\Stmt\ReturnNode;
use Elfennol\MonkeyPhp\Node\NodeInterface;

/**
 * @extends AbstractCompilerRule<ReturnNode>
 */
readonly class ReturnCompilerRule extends AbstractCompilerRule
{
    public function execute(CompilerContext $context, NodeInterface $node): void
    {
        if ($node->expr()->isSome()) {
            $context->process($node->expr()->unwrap());
            $this->emit($context, OpCode::ReturnValue);

            return;
        }

        $this->emit($context, OpCode::Return);
    }

    protected function support(NodeInterface $node): bool
    {
        return $node instanceof ReturnNode;
    }
}
