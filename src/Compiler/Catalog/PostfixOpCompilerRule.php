<?php

declare(strict_types=1);

namespace Elfennol\MonkeyPhp\Compiler\Catalog;

use Elfennol\MonkeyPhp\Coder\OpCode;
use Elfennol\MonkeyPhp\Compiler\AbstractCompilerRule;
use Elfennol\MonkeyPhp\Compiler\CompilerContext;
use Elfennol\MonkeyPhp\Compiler\CompilerException;
use Elfennol\MonkeyPhp\Compiler\CompilerExceptionType;
use Elfennol\MonkeyPhp\Node\Catalog\Expr\PostfixOpNode;
use Elfennol\MonkeyPhp\Node\NodeInterface;
use Elfennol\MonkeyPhp\Token\TokenType;

/**
 * @extends AbstractCompilerRule<PostfixOpNode>
 */
readonly class PostfixOpCompilerRule extends AbstractCompilerRule
{
    public function execute(CompilerContext $context, NodeInterface $node): void
    {
        $context->process($node->operand());

        $opCode = match ($node->operator()) {
            TokenType::Bang => OpCode::Factorial,
            default => throw new CompilerException(
                CompilerExceptionType::NodeNotSupported,
                ['node' => $node->debug()],
                'Unable to compile PostfixOp.'
            ),
        };

        $this->emit($context, $opCode);
    }

    protected function support(NodeInterface $node): bool
    {
        return $node instanceof PostfixOpNode;
    }
}
