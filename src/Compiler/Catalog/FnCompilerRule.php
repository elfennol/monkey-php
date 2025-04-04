<?php

declare(strict_types=1);

namespace Elfennol\MonkeyPhp\Compiler\Catalog;

use Elfennol\MonkeyPhp\Coder\OpCode;
use Elfennol\MonkeyPhp\Compiler\AbstractCompilerRule;
use Elfennol\MonkeyPhp\Compiler\CompilerContext;
use Elfennol\MonkeyPhp\Compiler\CompilerException;
use Elfennol\MonkeyPhp\Compiler\CompilerExceptionType;
use Elfennol\MonkeyPhp\Compiler\Symbol\Scope;
use Elfennol\MonkeyPhp\Node\Catalog\Expr\FnNode;
use Elfennol\MonkeyPhp\Node\NodeInterface;
use Elfennol\MonkeyPhp\SysObject\Catalog\CompiledFnSysObject;

/**
 * @extends AbstractCompilerRule<FnNode>
 */
readonly class FnCompilerRule extends AbstractCompilerRule
{
    public function execute(CompilerContext $context, NodeInterface $node): void
    {
        $parentCount = count($context->byteCodeWriter->constants());
        $fnName = $context->pendingFnName;
        $child = $context->createChildContext();

        if ($fnName->isSome()) {
            $child->symbolTable->defineFunction($fnName->unwrap());
        }

        foreach ($node->fnParams()->identifiers() as $identifier) {
            $child->symbolTable->define($identifier->name());
        }

        $child->process($node->body());

        $instructions = $child->byteCodeWriter->byteCode()->instructions;
        $lastByte = end($instructions);
        if ($lastByte === false) {
            $this->emit($child, OpCode::Return);
        } elseif (!in_array($lastByte->toInt(), [OpCode::ReturnValue->value, OpCode::Return->value], true)) {
            $this->emit($child, OpCode::ReturnValue);
        }

        foreach (array_slice($child->byteCodeWriter->constants(), $parentCount) as $c) {
            $context->byteCodeWriter->writeConstant($c);
        }

        $fnIndex = $context->byteCodeWriter->writeConstant(
            new CompiledFnSysObject($child->byteCodeWriter->byteCode()->instructions)
        );

        $freeSymbols = $child->symbolTable->freeSymbols();
        foreach ($freeSymbols as $freeSymbol) {
            $opCode = match ($freeSymbol->scope) {
                Scope::Local => OpCode::GetLocal,
                Scope::Free  => OpCode::GetFree,
                default      => throw new CompilerException(
                    CompilerExceptionType::UnexpectedScope,
                    ['scope' => $freeSymbol->scope->name],
                ),
            };
            $this->emit($context, $opCode, [$freeSymbol->index]);
        }

        $this->emit($context, OpCode::Closure, [$fnIndex, count($freeSymbols)]);
    }

    protected function support(NodeInterface $node): bool
    {
        return $node instanceof FnNode;
    }
}
