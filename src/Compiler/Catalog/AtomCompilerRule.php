<?php

declare(strict_types=1);

namespace Elfennol\MonkeyPhp\Compiler\Catalog;

use Elfennol\MonkeyPhp\Coder\OpCode;
use Elfennol\MonkeyPhp\Compiler\AbstractCompilerRule;
use Elfennol\MonkeyPhp\Compiler\CompilerContext;
use Elfennol\MonkeyPhp\Compiler\CompilerException;
use Elfennol\MonkeyPhp\Compiler\CompilerExceptionType;
use Elfennol\MonkeyPhp\Node\AtomExprNodeInterface;
use Elfennol\MonkeyPhp\Node\Catalog\Atom\BoolNode;
use Elfennol\MonkeyPhp\Node\Catalog\Atom\IntNode;
use Elfennol\MonkeyPhp\Node\Catalog\Atom\StringNode;
use Elfennol\MonkeyPhp\Node\NodeInterface;
use Elfennol\MonkeyPhp\SysObject\Catalog\IntSysObject;
use Elfennol\MonkeyPhp\SysObject\Catalog\StringSysObject;
use Elfennol\MonkeyPhp\Token\TokenType;

/**
 * @extends AbstractCompilerRule<AtomExprNodeInterface>
 */
readonly class AtomCompilerRule extends AbstractCompilerRule
{
    public function execute(CompilerContext $context, NodeInterface $node): void
    {
        match (true) {
            $node instanceof IntNode => $this->emitInt($context, $node),
            $node instanceof BoolNode => $this->emitBool($context, $node),
            $node instanceof StringNode => $this->emitString($context, $node),
            default => throw new CompilerException(
                CompilerExceptionType::NodeNotSupported,
                ['node' => $node->debug()],
                'Unable to compile Atom.'
            ),
        };
    }

    protected function support(NodeInterface $node): bool
    {
        return $node instanceof AtomExprNodeInterface;
    }

    private function emitInt(CompilerContext $context, IntNode $node): void
    {
        $this->emit(
            $context,
            OpCode::Const,
            [$context->byteCodeWriter->writeConstant(new IntSysObject($node->value()))]
        );
    }

    private function emitBool(CompilerContext $context, BoolNode $node): void
    {
        $this->emit(
            $context,
            TokenType::True->value === $node->value() ? OpCode::True : OpCode::False
        );
    }

    private function emitString(CompilerContext $context, StringNode $node): void
    {
        $this->emit(
            $context,
            OpCode::Const,
            [$context->byteCodeWriter->writeConstant(new StringSysObject($node->value()))]
        );
    }
}
