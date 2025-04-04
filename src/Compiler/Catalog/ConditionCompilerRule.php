<?php

declare(strict_types=1);

namespace Elfennol\MonkeyPhp\Compiler\Catalog;

use Elfennol\MonkeyPhp\Coder\OpCode;
use Elfennol\MonkeyPhp\Compiler\AbstractCompilerRule;
use Elfennol\MonkeyPhp\Compiler\CompilerContext;
use Elfennol\MonkeyPhp\Node\ConditionalNodeInterface;
use Elfennol\MonkeyPhp\Node\NodeInterface;

/**
 * @extends AbstractCompilerRule<ConditionalNodeInterface>
 */
readonly class ConditionCompilerRule extends AbstractCompilerRule
{
    public function execute(CompilerContext $context, NodeInterface $node): void
    {
        $context->process($node->expr());

        // 1. Record the current position for JumpNotTruthy
        $jumpNotTruthyPos = $context->byteCodeWriter->size();

        // 2. Emit placeholder (reserves 3 bytes)
        $this->emit($context, OpCode::JumpNotTruthy, [9999]);

        // 3. Compile the consequence (fills subsequent bytes)
        $context->process($node->consequence());

        // 4. Emit Jump to skip the alternative/unit when the consequence is executed
        $jumpAfterPos = $context->byteCodeWriter->size();
        $this->emit($context, OpCode::Jump, [9999]);

        // 5. Record the alternative/unit position
        $alternativePos = $context->byteCodeWriter->size();

        if ($node->alternative()->isSome()) {
            // Compile the alternative
            $context->process($node->alternative()->unwrap());
        } else {
            // Emit Unit so that if (false) has a value
            $this->emit($context, OpCode::Unit);
        }

        // 6. Record the position after the alternative/unit
        $afterAlternativePos = $context->byteCodeWriter->size();

        // 7. "Patch" JumpNotTruthy to point to the start of the alternative/unit
        $context->byteCodeWriter->replaceInstruction(
            $jumpNotTruthyPos,
            $this->make(OpCode::JumpNotTruthy, [$alternativePos]),
        );

        // 8. "Patch" Jump to point after the alternative/unit
        $context->byteCodeWriter->replaceInstruction(
            $jumpAfterPos,
            $this->make(OpCode::Jump, [$afterAlternativePos]),
        );
    }

    protected function support(NodeInterface $node): bool
    {
        return $node instanceof ConditionalNodeInterface;
    }
}
