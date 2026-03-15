<?php

declare(strict_types=1);

namespace Elfennol\MonkeyPhp\Tests\Compiler;

use Elfennol\MonkeyPhp\ByteCode\ByteCode;
use Elfennol\MonkeyPhp\ByteCode\ByteCodeReader;
use Elfennol\MonkeyPhp\Coder\Definitions;
use Elfennol\MonkeyPhp\Coder\OpCode;
use Elfennol\MonkeyPhp\Compiler\OperandsExtractor;
use Elfennol\MonkeyPhp\Utils\Type\Byte;
use PHPUnit\Framework\TestCase;

class OperandsExtractorTest extends TestCase
{
    private OperandsExtractor $extractor;

    protected function setUp(): void
    {
        $this->extractor = new OperandsExtractor(new Definitions());
    }

    public function testReadFromByteCodeWithSingleOperand(): void
    {
        $byteCodeReader = new ByteCodeReader(new ByteCode([Byte::fromInt(1), Byte::fromInt(2)], []));
        $operands = $this->extractor->readFromByteCode($byteCodeReader, OpCode::Const);

        $this->assertSame([258], $operands);
    }

    public function testReadFromByteCodeWithNoOperands(): void
    {
        $byteCodeReader = new ByteCodeReader(new ByteCode([], []));
        $operands = $this->extractor->readFromByteCode($byteCodeReader, OpCode::Add);

        $this->assertSame([], $operands);
    }
}
