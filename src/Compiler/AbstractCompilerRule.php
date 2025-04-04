<?php

declare(strict_types=1);

namespace Elfennol\MonkeyPhp\Compiler;

use Elfennol\MonkeyPhp\Coder\Coder;
use Elfennol\MonkeyPhp\Coder\OpCode;
use Elfennol\MonkeyPhp\Node\NodeInterface;
use Elfennol\MonkeyPhp\Utils\Type\Byte;

/**
 * @template TNode of NodeInterface
 */
abstract readonly class AbstractCompilerRule implements CompilerRuleInterface
{
    public function __construct(private Coder $coder)
    {
    }

    public function apply(CompilerContext $context, NodeInterface $node): bool
    {
        if (!$this->support($node)) {
            return false;
        }

        $this->execute($context, $node);

        return true;
    }

    /**
     * @param int[] $operands
     */
    protected function emit(CompilerContext $context, OpCode $opCode, array $operands = []): void
    {
        $context->byteCodeWriter->writeInstruction($this->make($opCode, $operands));
    }

    /**
     * @param int[] $operands
     *
     * @return Byte[]
     */
    protected function make(OpCode $opCode, array $operands = []): array
    {
        return $this->coder->make($opCode, $operands);
    }

    /**
     * @param TNode $node
     */
    abstract protected function execute(CompilerContext $context, NodeInterface $node): void;

    /**
     * @phpstan-assert-if-true TNode $node
     */
    abstract protected function support(NodeInterface $node): bool;
}
