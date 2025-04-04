<?php

declare(strict_types=1);

namespace Elfennol\MonkeyPhp\Compiler;

use Elfennol\MonkeyPhp\Node\NodeInterface;
use Elfennol\MonkeyPhp\Utils\Option\None;
use Elfennol\MonkeyPhp\Utils\Option\Option;

readonly class Compiler implements CompilerInterface
{
    public function __construct(private CompilerRules $rules, private CompilerContextBuilder $contextBuilder)
    {
    }

    public function compile(
        NodeInterface $node,
        Option $symbolTable = new None(),
        Option $constant = new None()
    ): CompilerResult {
        $context = $this->contextBuilder->build($this->process(...), $symbolTable, $constant);

        $context->process($node);

        return new CompilerResult(
            $context->byteCodeWriter->byteCode(),
            $context->symbolTable,
            $context->byteCodeWriter->constants()
        );
    }

    private function process(CompilerContext $context, NodeInterface $node): void
    {
        foreach ($this->rules->get() as $rule) {
            if (true === $rule->apply($context, $node)) {
                break;
            }
        }
    }
}
