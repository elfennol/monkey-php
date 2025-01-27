<?php

declare(strict_types=1);

namespace Elfennol\MonkeyPhp\Tests\Repl;

use Elfennol\MonkeyPhp\Evaluator\EvaluatorException;
use Elfennol\MonkeyPhp\Evaluator\EvaluatorExceptionType;
use Elfennol\MonkeyPhp\Repl\Writer;
use Elfennol\MonkeyPhp\SysObject\Catalog\ArraySysObject;
use Elfennol\MonkeyPhp\SysObject\Catalog\BoolSysObject;
use Elfennol\MonkeyPhp\SysObject\Catalog\HashMapItemSysObject;
use Elfennol\MonkeyPhp\SysObject\Catalog\HashMapSysObject;
use Elfennol\MonkeyPhp\SysObject\Catalog\IntSysObject;
use Elfennol\MonkeyPhp\SysObject\Catalog\StringSysObject;
use Elfennol\MonkeyPhp\SysObject\SysObjectInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class WriterTest extends TestCase
{
    private Writer $writer;

    protected function setUp(): void
    {
        $this->writer = new Writer();
    }

    public function testDisplayBanner(): void
    {
        $this->writer->displayBanner();
        $this->expectOutputString(sprintf('Monkey programming language.%s', "\n"));
    }

    public function testGetPrompt(): void
    {
        self::assertSame('monkey > ', $this->writer->getPrompt());
    }

    #[DataProvider('displayProvider')]
    public function testDisplay(SysObjectInterface $sysObject, string $expected): void
    {
        $this->writer->display($sysObject);
        $this->expectOutputString(sprintf('%s%s', $expected, "\n"));
    }

    public function testDisplayError(): void
    {
        $exception = new EvaluatorException(
            EvaluatorExceptionType::ConditionInvalid,
            ['foo' => 'bar'],
            'msg',
        );

        $this->writer->displayError($exception);
        $expected = sprintf('Monkey error: %s: %s', EvaluatorExceptionType::ConditionInvalid->name, 'msg');
        $expected .= "\n";
        $expected .= json_encode(['foo' => 'bar'], JSON_PRETTY_PRINT);
        $expected .= "\n";
        $this->expectOutputString($expected);
    }

    public function testDisplayRuntimeError(): void
    {
        $this->writer->displayRuntimeError('msg');
        $this->expectOutputString(sprintf('Monkey error: %s%s', 'msg', "\n"));
    }

    /**
     * @return array{array{SysObjectInterface, string}}
     */
    public static function displayProvider(): array
    {
        return [
            [new BoolSysObject('true'), 'true'],
            [new IntSysObject('42'), '42'],
            [new StringSysObject('foo'), 'foo'],
            [new ArraySysObject([new IntSysObject('1'), new IntSysObject('2')]), '[1, 2]'],
            [
                new HashMapSysObject([
                    'hash1' => new HashMapItemSysObject(
                        new StringSysObject('foo1'),
                        new StringSysObject('bar1')
                    ),
                    'hash2' => new HashMapItemSysObject(
                        new StringSysObject('foo2'),
                        new StringSysObject('bar2')
                    )
                ]),
                '{"foo1": "bar1", "foo2": "bar2"}'
            ],
        ];
    }
}
