<?php

declare(strict_types=1);

namespace Elfennol\MonkeyPhp\Compiler\Catalog;

use Elfennol\MonkeyPhp\Coder\OpCode;
use Elfennol\MonkeyPhp\Compiler\AbstractCompilerRule;
use Elfennol\MonkeyPhp\Compiler\CompilerContext;
use Elfennol\MonkeyPhp\Node\ExprNodeInterface;
use Elfennol\MonkeyPhp\Node\NodeInterface;
use Elfennol\MonkeyPhp\Node\ProgramNodeInterface;

/**
 * @extends AbstractCompilerRule<ProgramNodeInterface>
 */
readonly class ProgramCompilerRule extends AbstractCompilerRule
{
    public function execute(CompilerContext $context, NodeInterface $node): void
    {
        foreach ($node->stmts() as $stmt) {
            $context->process($stmt);
            if ($stmt instanceof ExprNodeInterface) {
                $this->emit($context, OpCode::Pop);
            }
        }
    }

    protected function support(NodeInterface $node): bool
    {
        return $node instanceof ProgramNodeInterface;
    }
}
