<?php

declare(strict_types=1);

namespace Elfennol\MonkeyPhp\Compiler;

use Elfennol\MonkeyPhp\ByteCode\ByteCode;
use Elfennol\MonkeyPhp\ByteCode\ByteCodeReaderBuilder;
use Elfennol\MonkeyPhp\Coder\OpCode;
use Elfennol\MonkeyPhp\SysObject\Builtins\BuiltinName;
use Elfennol\MonkeyPhp\SysObject\Catalog\CompiledFnSysObject;
use Elfennol\MonkeyPhp\SysObject\ConstantTypeInterface;
use Elfennol\MonkeyPhp\SysObject\SysObjectType;
use ValueError;

readonly class AssemblerProducer
{
    public function __construct(
        private ByteCodeReaderBuilder $byteCodeReaderBuilder,
        private OperandsExtractor $operandsExtractor,
    ) {
    }

    /**
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function assemble(ByteCode $byteCode): string
    {
        /** @var array<int, int> $fnIndexMap maps constant pointer → fn label number */
        $fnIndexMap = $this->buildFnIndexMap($byteCode->constants);

        $lines = ['; section .data'];

        foreach ($byteCode->constants as $pointer => $constant) {
            $lines[] = sprintf(';    %05d %s', $pointer, $this->formatConstant($constant, $pointer, $fnIndexMap));
        }

        $lines[] = '';

        foreach ($byteCode->constants as $pointer => $constant) {
            if (!($constant instanceof CompiledFnSysObject)) {
                continue;
            }

            $fnLabel = sprintf('fn_%d', $fnIndexMap[$pointer]);
            $lines[] = $fnLabel . ':';

            $fnByteCode = new ByteCode($constant->instructions(), $byteCode->constants);
            $fnByteCodeReader = $this->byteCodeReaderBuilder->build($fnByteCode);
            $byteOption = $fnByteCodeReader->readByte();
            while ($byteOption->isSome()) {
                $instruction = $byteOption->unwrap();
                $currentInstPointer = $fnByteCodeReader->getInstPointer() - 1;
                try {
                    $opCode = OpCode::from($instruction->toInt());
                } catch (ValueError) {
                    throw new CompilerException(
                        CompilerExceptionType::InvalidOpCode,
                        ['instPointer' => $currentInstPointer, 'opCode' => $instruction->toInt()],
                    );
                }

                $operands = $this->operandsExtractor->readFromByteCode($fnByteCodeReader, $opCode);

                $lines[] = empty($operands)
                    ? '    ' . $opCode->name
                    : sprintf('    %s %s', $opCode->name, $this->formatOperands($opCode, $operands));

                $byteOption = $fnByteCodeReader->readByte();
            }

            $lines[] = '';
        }

        $byteCodeReader = $this->byteCodeReaderBuilder->build($byteCode);
        $byteOption = $byteCodeReader->readByte();
        while ($byteOption->isSome()) {
            $instruction = $byteOption->unwrap();
            $currentInstPointer = $byteCodeReader->getInstPointer() - 1;
            try {
                $opCode = OpCode::from($instruction->toInt());
            } catch (ValueError) {
                throw new CompilerException(
                    CompilerExceptionType::InvalidOpCode,
                    ['instPointer' => $currentInstPointer, 'opCode' => $instruction->toInt()],
                );
            }

            $operands = $this->operandsExtractor->readFromByteCode($byteCodeReader, $opCode);

            $lines[] = empty($operands)
                ? $opCode->name
                : sprintf('%s %s', $opCode->name, $this->formatOperands($opCode, $operands));

            $byteOption = $byteCodeReader->readByte();
        }

        return implode("\n", $lines) . "\n";
    }

    /**
     * @param int[] $operands
     */
    private function formatOperands(OpCode $opCode, array $operands): string
    {
        if (OpCode::GetBuiltin === $opCode) {
            return BuiltinName::cases()[$operands[0]]->value;
        }

        return implode(' ', $operands);
    }

    /**
     * @param array<int, ConstantTypeInterface> $constants
     * @return array<int, int>
     */
    private function buildFnIndexMap(array $constants): array
    {
        $fnIndexMap = [];
        $fnCounter = 0;

        foreach ($constants as $pointer => $constant) {
            if ($constant->type() === SysObjectType::CompiledFunction) {
                $fnIndexMap[$pointer] = $fnCounter++;
            }
        }

        return $fnIndexMap;
    }

    /**
     * @param array<int, int> $fnIndexMap
     */
    private function formatConstant(ConstantTypeInterface $constant, int $pointer, array $fnIndexMap): string
    {
        if ($constant->type() === SysObjectType::CompiledFunction) {
            return sprintf('@fn_%d', $fnIndexMap[$pointer]);
        }

        $str = $constant->__toString();

        if ($constant->type() === SysObjectType::String) {
            return sprintf('"%s"', $str);
        }

        return $str;
    }
}
