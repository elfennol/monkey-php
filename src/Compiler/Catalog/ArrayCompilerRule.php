<?php

declare(strict_types=1);

namespace Elfennol\MonkeyPhp\Compiler\Catalog;

use Elfennol\MonkeyPhp\Coder\OpCode;
use Elfennol\MonkeyPhp\Compiler\AbstractCompilerRule;
use Elfennol\MonkeyPhp\Compiler\CompilerContext;
use Elfennol\MonkeyPhp\Node\Catalog\Expr\ArrayNode;
use Elfennol\MonkeyPhp\Node\NodeInterface;

/**
 * @extends AbstractCompilerRule<ArrayNode>
 */
readonly class ArrayCompilerRule extends AbstractCompilerRule
{
    public function execute(CompilerContext $context, NodeInterface $node): void
    {
        foreach ($node->elements() as $element) {
            $context->process($element);
        }

        $this->emit($context, OpCode::Array, [count($node->elements())]);
    }

    protected function support(NodeInterface $node): bool
    {
        return $node instanceof ArrayNode;
    }
}
