<?php

declare(strict_types=1);

namespace Elfennol\MonkeyPhp\Vm;

use Elfennol\MonkeyPhp\ByteCode\ByteCode;
use Elfennol\MonkeyPhp\Coder\OpCode;
use Elfennol\MonkeyPhp\Utils\Option\None;
use Elfennol\MonkeyPhp\Utils\Option\Option;
use Elfennol\MonkeyPhp\Vm\SingletonObj\UnitObj;
use ValueError;

/**
 * @SuppressWarnings(PHPMD.ShortClassName)
 */
readonly class Vm implements VmInterface
{
    public function __construct(private VmRules $rules, private VmContextBuilder $contextBuilder)
    {
    }

    /**
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function run(ByteCode $byteCode, Option $globals = new None()): VmResult
    {
        $context = $this->contextBuilder->build($byteCode, $globals);

        $this->process($context);

        $last = $context->stack->last();

        $sysObject = $last->isSome() ? $last->unwrap() : UnitObj::get();

        return new VmResult($sysObject, $context->globals);
    }

    /**
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    private function process(VmContext $context): void
    {
        $byteOption = $context->currentReader()->readByte();
        while ($byteOption->isSome()) {
            $instruction = $byteOption->unwrap();

            try {
                $opCode = OpCode::from($instruction->toInt());
            } catch (ValueError) {
                throw new VmException(
                    VmExceptionType::InvalidOpCode,
                    ['instPointer' => $context->currentReader()->getInstPointer(), 'opCode' => $instruction->toInt()],
                );
            }

            foreach ($this->rules->get() as $rule) {
                if (true === $rule->apply($context, $opCode)) {
                    break;
                }
            }

            $byteOption = $context->currentReader()->readByte();
        }
    }
}
