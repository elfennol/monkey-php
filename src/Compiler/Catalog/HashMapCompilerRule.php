<?php

declare(strict_types=1);

namespace Elfennol\MonkeyPhp\Compiler\Catalog;

use Elfennol\MonkeyPhp\Coder\OpCode;
use Elfennol\MonkeyPhp\Compiler\AbstractCompilerRule;
use Elfennol\MonkeyPhp\Compiler\CompilerContext;
use Elfennol\MonkeyPhp\Node\Catalog\Expr\HashMapNode;
use Elfennol\MonkeyPhp\Node\NodeInterface;

/**
 * @extends AbstractCompilerRule<HashMapNode>
 */
readonly class HashMapCompilerRule extends AbstractCompilerRule
{
    public function execute(CompilerContext $context, NodeInterface $node): void
    {
        foreach ($node->elements() as $element) {
            $context->process($element->key());
            $context->process($element->value());
        }

        $this->emit($context, OpCode::HashMap, [count($node->elements()) * 2]);
    }

    protected function support(NodeInterface $node): bool
    {
        return $node instanceof HashMapNode;
    }
}
