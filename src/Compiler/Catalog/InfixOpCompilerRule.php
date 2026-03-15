<?php

declare(strict_types=1);

namespace Elfennol\MonkeyPhp\Compiler\Catalog;

use Elfennol\MonkeyPhp\Coder\OpCode;
use Elfennol\MonkeyPhp\Compiler\AbstractCompilerRule;
use Elfennol\MonkeyPhp\Compiler\CompilerContext;
use Elfennol\MonkeyPhp\Compiler\CompilerException;
use Elfennol\MonkeyPhp\Compiler\CompilerExceptionType;
use Elfennol\MonkeyPhp\Node\Catalog\Expr\InfixOpNode;
use Elfennol\MonkeyPhp\Node\NodeInterface;
use Elfennol\MonkeyPhp\Token\TokenType;

/**
 * @extends AbstractCompilerRule<InfixOpNode>
 */
readonly class InfixOpCompilerRule extends AbstractCompilerRule
{
    public function execute(CompilerContext $context, NodeInterface $node): void
    {
        if (TokenType::Lt === $node->operator()) {
            $context->process($node->rightOperand());
            $context->process($node->leftOperand());
            $this->emit($context, OpCode::GreaterThan);

            return;
        }

        $context->process($node->leftOperand());
        $context->process($node->rightOperand());

        $opCode = match ($node->operator()) {
            TokenType::Plus => OpCode::Add,
            TokenType::Minus => OpCode::Sub,
            TokenType::Asterisk => OpCode::Mul,
            TokenType::Slash => OpCode::Div,
            TokenType::DoubleAsterisk => OpCode::Pow,
            TokenType::Eq => OpCode::Equal,
            TokenType::NotEq => OpCode::NotEqual,
            TokenType::Gt => OpCode::GreaterThan,
            default => throw new CompilerException(
                CompilerExceptionType::NodeNotSupported,
                ['node' => $node->debug()],
                'Unable to compile InfixOp.'
            ),
        };

        $this->emit($context, $opCode);
    }

    protected function support(NodeInterface $node): bool
    {
        return $node instanceof InfixOpNode;
    }
}
