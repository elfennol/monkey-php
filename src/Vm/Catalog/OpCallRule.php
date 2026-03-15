<?php

declare(strict_types=1);

namespace Elfennol\MonkeyPhp\Vm\Catalog;

use Elfennol\MonkeyPhp\ByteCode\ByteCode;
use Elfennol\MonkeyPhp\ByteCode\ByteCodeReader;
use Elfennol\MonkeyPhp\Coder\OpCode;
use Elfennol\MonkeyPhp\Compiler\OperandsExtractor;
use Elfennol\MonkeyPhp\SysObject\Builtins\BuiltinException;
use Elfennol\MonkeyPhp\SysObject\Catalog\BuiltinSysObject;
use Elfennol\MonkeyPhp\SysObject\Catalog\ClosureSysObject;
use Elfennol\MonkeyPhp\Vm\AbstractVmRule;
use Elfennol\MonkeyPhp\Vm\Frame;
use Elfennol\MonkeyPhp\Vm\VmContext;
use Elfennol\MonkeyPhp\Vm\VmException;
use Elfennol\MonkeyPhp\Vm\VmExceptionType;
use Elfennol\MonkeyPhp\Utils\Option\Some;

/**
 * @extends AbstractVmRule<OpCode::Call>
 */
readonly class OpCallRule extends AbstractVmRule
{
    public function __construct(private OperandsExtractor $operandsExtractor)
    {
    }

    protected function execute(VmContext $context, OpCode $opCode): void
    {
        $operands = $this->operandsExtractor->readFromByteCode($context->currentReader(), $opCode);
        $numArgs = $operands[0];

        $fn = $context->stack->pop();

        if ($fn instanceof BuiltinSysObject) {
            $args = [];
            for ($i = 0; $i < $numArgs; $i++) {
                array_unshift($args, $context->stack->pop());
            }
            try {
                $context->stack->push(($fn->builtinFn())(...$args));
            } catch (BuiltinException $exception) {
                throw new VmException(VmExceptionType::BuiltinCallFailed, $exception->getContext());
            }

            return;
        }

        if (!$fn instanceof ClosureSysObject) {
            throw new VmException(VmExceptionType::InvalidSysObject, ['expected' => ClosureSysObject::class]);
        }

        $constants = $context->currentReader()->getByteCode()->constants;
        $frame = new Frame(
            new ByteCodeReader(new ByteCode($fn->compiledFn()->instructions(), $constants)),
            new Some($fn),
        );

        for ($i = $numArgs - 1; $i >= 0; $i--) {
            $frame->locals[$i] = $context->stack->pop();
        }

        $context->callStack->push($frame);
    }

    protected function support(OpCode $opCode): bool
    {
        return OpCode::Call === $opCode;
    }
}
