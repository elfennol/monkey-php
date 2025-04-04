<?php

declare(strict_types=1);

namespace Elfennol\MonkeyPhp\Compiler\Catalog;

use Elfennol\MonkeyPhp\Coder\OpCode;
use Elfennol\MonkeyPhp\Compiler\AbstractCompilerRule;
use Elfennol\MonkeyPhp\Compiler\CompilerContext;
use Elfennol\MonkeyPhp\Compiler\Symbol\Scope;
use Elfennol\MonkeyPhp\Node\Catalog\Expr\FnNode;
use Elfennol\MonkeyPhp\Node\Catalog\Stmt\LetNode;
use Elfennol\MonkeyPhp\Node\NodeInterface;

/**
 * @extends AbstractCompilerRule<LetNode>
 */
readonly class LetCompilerRule extends AbstractCompilerRule
{
    public function execute(CompilerContext $context, NodeInterface $node): void
    {
        $ctx = $node->expr() instanceof FnNode
            ? $context->withPendingFnName($node->identifier()->name())
            : $context;
        $ctx->process($node->expr());
        $symbol = $context->symbolTable->define($node->identifier()->name());
        $opCode = $symbol->scope === Scope::Local ? OpCode::SetLocal : OpCode::SetGlobal;
        $this->emit($context, $opCode, [$symbol->index]);
    }

    protected function support(NodeInterface $node): bool
    {
        return $node instanceof LetNode;
    }
}
