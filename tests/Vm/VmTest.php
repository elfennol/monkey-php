<?php

declare(strict_types=1);

namespace Elfennol\MonkeyPhp\Tests\Vm;

use Elfennol\MonkeyPhp\SysObject\AtomSysObjectInterface;
use Elfennol\MonkeyPhp\SysObject\Catalog\ArraySysObject;
use Elfennol\MonkeyPhp\SysObject\Catalog\BoolSysObject;
use Elfennol\MonkeyPhp\SysObject\Catalog\HashMapSysObject;
use Elfennol\MonkeyPhp\SysObject\Catalog\ClosureSysObject;
use Elfennol\MonkeyPhp\SysObject\Catalog\IntSysObject;
use Elfennol\MonkeyPhp\SysObject\Catalog\StringSysObject;
use Elfennol\MonkeyPhp\SysObject\Catalog\UnitSysObject;
use Elfennol\MonkeyPhp\Tests\CompilerFactoryTrait;
use Elfennol\MonkeyPhp\Tests\LexerFactoryTrait;
use Elfennol\MonkeyPhp\Tests\ParserFactoryTrait;
use Elfennol\MonkeyPhp\Tests\VmFactoryTrait;
use Elfennol\MonkeyPhp\Vm\Vm;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Stringable;

class VmTest extends TestCase
{
    use LexerFactoryTrait;
    use ParserFactoryTrait;
    use CompilerFactoryTrait;
    use VmFactoryTrait;

    private Vm $vm;

    protected function setUp(): void
    {
        $this->vm = $this->createVm();
    }

    #[DataProvider('vmProvider')]
    public function testVm(string $input, string $expectedValue, string $expectedClass): void
    {
        $parser = $this->createParser();
        $nodes = $parser->parse($this->createLexer($input));
        ob_start();
        $sysObject = $this->vm->run($this->createCompiler()->compile($nodes)->byteCode)->sysObject;
        ob_end_clean();

        if (!class_exists($expectedClass) && !interface_exists($expectedClass)) {
            self::fail(sprintf('%s does not exist.', $expectedClass));
        }

        self::assertInstanceOf($expectedClass, $sysObject);

        if ($sysObject instanceof Stringable) {
            self::assertSame($expectedValue, $sysObject->__toString());
        }
    }

    /**
     * @return array<string, string[]>
     */
    public static function vmProvider(): array
    {
        return [
            'One value' => ['42;', '42', IntSysObject::class],
            'Simple infix' => ['2 + 3; 3 + 4;', '7', IntSysObject::class],
            'Sub infix' => ['2 - 3;', '-1', IntSysObject::class],
            'Mul infix' => ['2 * 3;', '6', IntSysObject::class],
            'Div infix' => ['6 / 3;', '2', IntSysObject::class],
            'True value' => ['true;', 'true', BoolSysObject::class],
            'False value' => ['false;', 'false', BoolSysObject::class],
            'LesserThan True' => ['2 < 3;', 'true', BoolSysObject::class],
            'LesserThan False' => ['2 > 3;', 'false', BoolSysObject::class],
            'GreaterThan True' => ['3 > 2;', 'true', BoolSysObject::class],
            'GreaterThan False' => ['2 > 3;', 'false', BoolSysObject::class],
            'Equal int True' => ['2 == 2;', 'true', BoolSysObject::class],
            'Equal int False' => ['2 == 3;', 'false', BoolSysObject::class],
            'Equal bool True' => ['true == true;', 'true', BoolSysObject::class],
            'Equal bool False' => ['true == false;', 'false', BoolSysObject::class],
            'NotEqual int True' => ['2 != 3;', 'true', BoolSysObject::class],
            'NotEqual int False' => ['2 != 2;', 'false', BoolSysObject::class],
            'NotEqual bool True' => ['true != false;', 'true', BoolSysObject::class],
            'NotEqual bool False' => ['true != true;', 'false', BoolSysObject::class],
            'Prefix minus' => ['-2;', '-2', IntSysObject::class],
            'Prefix minus with infix' => ['-2 + 3;', '1', IntSysObject::class],
            'Prefix bang' => ['!true;', 'false', BoolSysObject::class],
            'Prefix double bang' => ['!!true;', 'true', BoolSysObject::class],
            'Factorial' => ['3!;', '6', IntSysObject::class],
            'Power' => ['2 ** 3;', '8', IntSysObject::class],
            'Condition if True' => ['if (true) { 10 };', '10', IntSysObject::class],
            'Condition if False' => ['if (false) { 10 };', '', UnitSysObject::class],
            'Condition if else True' => ['if (true) { 10 } else { 20 };', '10', IntSysObject::class],
            'Condition if else False' => ['if (false) { 10 } else { 20 };', '20', IntSysObject::class],
            'Condition if' => ['if (true) { 10 }; 100;', '100', IntSysObject::class],
            'Condition if else' => ['if (true) { 10 } else { 20 }; 100;', '100', IntSysObject::class],
            'Binding let' => ['let foo = 2; let bar = foo; bar;', '2', IntSysObject::class],
            'String' => ['"monkey";', "monkey", StringSysObject::class],
            'String concat' => ['"mon" + "key";', "monkey", StringSysObject::class],
            'Array empty' => ['[];', '[]', ArraySysObject::class],
            'Array not empty' => ['[2, 3];', '[2, 3]', ArraySysObject::class],
            'Array with expression' => ['[2 + 3, 5 + 6];', '[5, 11]', ArraySysObject::class],
            'Hash empty' => ['{};', '{}', HashMapSysObject::class],
            'Hash simple' => ['{1: 2, 3: 4};', '{1: 2, 3: 4}', HashMapSysObject::class],
            'Hash expression' => ['{1: 2 + 3, 4 + 5: 6};', '{1: 5, 9: 6}', HashMapSysObject::class],
            'Index array' => ['[1, 2, 3][1 + 1];', '3', AtomSysObjectInterface::class],
            'Index hash map' => ['{1 : 2}[2 - 1];', '2', AtomSysObjectInterface::class],
            'Function call simple' => ['fn() { 24 }();', '24', IntSysObject::class],
            'Function call with let' => ['let foo = fn() { 24 }; foo();', '24', IntSysObject::class],
            'Function call returns unit' => ['fn() {}();', '', UnitSysObject::class],
            'Function call nested' => [
                'let foo = fn() { 1 }; let bar = fn() { foo() + 2 }; bar();',
                '3',
                IntSysObject::class
            ],
            'Function call chained' => [
                'let foo = fn() { 2 }; let bar = fn() { foo }; bar()();',
                '2',
                IntSysObject::class
            ],
            'Local binding simple' => ['fn() { let x = 5; x }();', '5', IntSysObject::class],
            'Local binding with global' => [
                'let globalSeed = 50; let minusOne = fn() { let num = 1; globalSeed - num }; let minusTwo = fn() { let num = 2; globalSeed - num }; minusOne() + minusTwo();',
                '97',
                IntSysObject::class,
            ],
            'Function call with one arg and empty body' => ['let foo = fn(a) { }; foo(42);', '', UnitSysObject::class],
            'Function call with two args' => ['let foo = fn(a, b) { a + b }; foo(2, 3);', '5', IntSysObject::class],
            'Function call with expression args' => [
                'let foo = fn(a, b) { a + b }; foo(2 + 3, 4);',
                '9',
                IntSysObject::class
            ],
            'Builtin len empty array' => ['len([]);', '0', IntSysObject::class],
            'Builtin push' => ['push([], 1);', '[1]', ArraySysObject::class],
            'Builtin echo' => ['echo("foo");', '', UnitSysObject::class],
            'Builtin first' => ['first([1, 2]);', '1', AtomSysObjectInterface::class],
            'Builtin last' => ['last([1, 2]);', '2', AtomSysObjectInterface::class],
            'Builtin rest' => ['rest([1, 2, 3]);', '[2, 3]', ArraySysObject::class],
            'Closure newAdder' => [
                'let newAdder = fn(a, b) { fn(c) { a + b + c }; }; let adder = newAdder(1, 2); adder(8);',
                '11',
                IntSysObject::class,
            ],
            'Closure self-reference' => [
                'let wrapper = fn() { let self = fn() { self }; self }; wrapper();',
                'closure',
                ClosureSysObject::class,
            ],
        ];
    }
}
