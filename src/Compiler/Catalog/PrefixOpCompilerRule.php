<?php

declare(strict_types=1);

namespace Elfennol\MonkeyPhp\Compiler\Catalog;

use Elfennol\MonkeyPhp\Coder\OpCode;
use Elfennol\MonkeyPhp\Compiler\AbstractCompilerRule;
use Elfennol\MonkeyPhp\Compiler\CompilerContext;
use Elfennol\MonkeyPhp\Compiler\CompilerException;
use Elfennol\MonkeyPhp\Compiler\CompilerExceptionType;
use Elfennol\MonkeyPhp\Node\Catalog\Expr\PrefixOpNode;
use Elfennol\MonkeyPhp\Node\NodeInterface;
use Elfennol\MonkeyPhp\Token\TokenType;

/**
 * @extends AbstractCompilerRule<PrefixOpNode>
 */
readonly class PrefixOpCompilerRule extends AbstractCompilerRule
{
    public function execute(CompilerContext $context, NodeInterface $node): void
    {
        $context->process($node->operand());

        $opCode = match ($node->operator()) {
            TokenType::Bang => OpCode::Bang,
            TokenType::Minus => OpCode::Minus,
            default => throw new CompilerException(
                CompilerExceptionType::NodeNotSupported,
                ['node' => $node->debug()],
                'Unable to compile PrefixOp.'
            ),
        };

        $this->emit($context, $opCode);
    }

    protected function support(NodeInterface $node): bool
    {
        return $node instanceof PrefixOpNode;
    }
}
