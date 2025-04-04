<?php

declare(strict_types=1);

namespace Elfennol\MonkeyPhp\Compiler;

use Closure;
use Elfennol\MonkeyPhp\ByteCode\ByteCodeWriter;
use Elfennol\MonkeyPhp\Compiler\Symbol\SymbolTable;
use Elfennol\MonkeyPhp\Node\NodeInterface;
use Elfennol\MonkeyPhp\Utils\Option\None;
use Elfennol\MonkeyPhp\Utils\Option\Option;
use Elfennol\MonkeyPhp\Utils\Option\Some;

readonly class CompilerContext
{
    /**
     * @param Closure(CompilerContext, NodeInterface): void $processor
     * @param Option<string> $pendingFnName
     */
    public function __construct(
        private Closure $processor,
        public ByteCodeWriter $byteCodeWriter,
        public SymbolTable $symbolTable,
        public Option $pendingFnName = new None(),
    ) {
    }

    public function withPendingFnName(string $name): self
    {
        return new self($this->processor, $this->byteCodeWriter, $this->symbolTable, new Some($name));
    }

    public function process(NodeInterface $node): void
    {
        ($this->processor)($this, $node);
    }

    public function createChildContext(): self
    {
        return new self(
            $this->processor,
            new ByteCodeWriter(new Some($this->byteCodeWriter->constants())),
            new SymbolTable(new Some($this->symbolTable)),
        );
    }
}
