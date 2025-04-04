<?php

declare(strict_types=1);

namespace Elfennol\MonkeyPhp\Tests\Code;

use Elfennol\MonkeyPhp\Coder\Coder;
use Elfennol\MonkeyPhp\Coder\CoderException;
use Elfennol\MonkeyPhp\Coder\CoderExceptionType;
use Elfennol\MonkeyPhp\Coder\Definition;
use Elfennol\MonkeyPhp\Coder\Definitions;
use Elfennol\MonkeyPhp\Coder\OpCode;
use Elfennol\MonkeyPhp\Utils\Type\Byte;
use Elfennol\MonkeyPhp\Utils\Type\NumberHelper;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class CoderTest extends TestCase
{
    private Coder $coder;
    private Definitions&MockObject $definitions;

    protected function setUp(): void
    {
        $this->definitions = $this->createMock(Definitions::class);

        $this->coder = new Coder($this->definitions, new NumberHelper());
    }

    /**
     * @param int[] $operands
     * @param Byte[] $expected
     */
    #[DataProvider('makeProvider')]
    public function testMake(OpCode $opCode, array $operands, array $expected): void
    {
        $this->definitions->method('get')->willReturn($this->definitionsFixtures());

        $instruction = $this->coder->make($opCode, $operands);
        $index = 0;
        foreach ($instruction as $value) {
            self::assertInstanceOf(Byte::class, $value);
            self::assertSame($expected[$index]->toInt(), $value->toInt());
            $index++;
        }
    }

    public function testMakeWhenInvalidOperand(): void
    {
        $this->definitions->method('get')->willReturn($this->definitionsFixtures());

        try {
            $this->coder->make(OpCode::Const, [65536]);
            self::fail('Expected CoderException');
        } catch (CoderException $coderException) {
            self::assertSame(CoderExceptionType::OperandInvalid, $coderException->getType());
            self::assertEquals(
                ['mneOpCode' => OpCode::Const->mnemonic(), 'operand' => 65536],
                $coderException->getContext()
            );
        }
    }

    public function testMakeWhenInvalidOpCode(): void
    {
        $this->definitions->method('get')->willReturn([]);

        try {
            $this->coder->make(OpCode::Const, [65534]);
            self::fail('Expected CoderException');
        } catch (CoderException $coderException) {
            self::assertSame(CoderExceptionType::OpCodeInvalid, $coderException->getType());
            self::assertEquals(['mneOpCode' => OpCode::Const->mnemonic()], $coderException->getContext());
        }
    }

    /**
     * @return array<string, array{OpCode, int[], Byte[]}>
     *
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public static function makeProvider(): array
    {
        return [
            'OpCode const with a 2 bytes operand' => [
                OpCode::Const,
                [65534],
                [Byte::fromInt(OpCode::Const->value), Byte::fromInt(255), Byte::fromInt(254)]
            ],
            'OpCode const with a 1 byte operand' => [
                OpCode::Const,
                [254],
                [Byte::fromInt(OpCode::Const->value), Byte::fromInt(0), Byte::fromInt(254)]
            ],
        ];
    }

    /**
     * @return array<int, Definition>
     */
    private function definitionsFixtures(): array
    {
        return [
            OpCode::Const->value => new Definition(OpCode::Const, [2]),
        ];
    }
}
