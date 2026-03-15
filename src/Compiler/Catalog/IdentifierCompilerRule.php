<?php

declare(strict_types=1);

namespace Elfennol\MonkeyPhp\Compiler\Catalog;

use Elfennol\MonkeyPhp\Coder\OpCode;
use Elfennol\MonkeyPhp\Compiler\AbstractCompilerRule;
use Elfennol\MonkeyPhp\Compiler\CompilerContext;
use Elfennol\MonkeyPhp\Compiler\Symbol\Scope;
use Elfennol\MonkeyPhp\Node\Catalog\Expr\IdentifierNode;
use Elfennol\MonkeyPhp\Node\NodeInterface;

/**
 * @extends AbstractCompilerRule<IdentifierNode>
 */
readonly class IdentifierCompilerRule extends AbstractCompilerRule
{
    public function execute(CompilerContext $context, NodeInterface $node): void
    {
        $symbol = $context->symbolTable->resolve($node->name());

        if ($symbol->scope === Scope::Function) {
            $this->emit($context, OpCode::CurrentClosure);

            return;
        }

        $opCode = match ($symbol->scope) {
            Scope::Local   => OpCode::GetLocal,
            Scope::Global  => OpCode::GetGlobal,
            Scope::Builtin => OpCode::GetBuiltin,
            Scope::Free    => OpCode::GetFree,
        };
        $this->emit($context, $opCode, [$symbol->index]);
    }

    protected function support(NodeInterface $node): bool
    {
        return $node instanceof IdentifierNode;
    }
}
