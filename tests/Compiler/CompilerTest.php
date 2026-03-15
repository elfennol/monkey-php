<?php

declare(strict_types=1);

namespace Elfennol\MonkeyPhp\Tests\Compiler;

use Elfennol\MonkeyPhp\Compiler\AssemblerProducer;
use Elfennol\MonkeyPhp\Compiler\Compiler;
use Elfennol\MonkeyPhp\Tests\CompilerFactoryTrait;
use Elfennol\MonkeyPhp\Tests\LexerFactoryTrait;
use Elfennol\MonkeyPhp\Tests\ParserFactoryTrait;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class CompilerTest extends TestCase
{
    use LexerFactoryTrait;
    use ParserFactoryTrait;
    use CompilerFactoryTrait;

    private Compiler $compiler;
    private AssemblerProducer $assemblerProducer;

    protected function setUp(): void
    {
        $this->compiler = $this->createCompiler();
        $this->assemblerProducer = $this->createAssembler();
    }

    #[DataProvider('compileProvider')]
    public function testCompile(string $input, string $expected): void
    {
        $parser = $this->createParser();
        $nodes = $parser->parse($this->createLexer($input));

        self::assertSame($expected, $this->assemblerProducer->assemble($this->compiler->compile($nodes)->byteCode));
    }

    /**
     * @return string[][]
     *
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public static function compileProvider(): array
    {
        return [
            'One value' => ['42;', self::asmOneValue()],
            'All infix' => ['2 + 3; 3 - 4; 3 * 4; 6 / 3;', self::asmAllInfix()],
            'Boolean' => ['true; false;', self::asmBoolean()],
            'Comparaison' => ['2 > 3;2 < 3; 2 == 3; 2 != 3;', self::asmComparaison()],
            'Prefix minus' => ['-2;', self::asmPrefixMinus()],
            'Prefix boolean negation' => ['!true;', self::asmPrefixBang()],
            'Postfix Factorial' => ['3!;', self::asmPostfixFactorial()],
            'Infix power' => ['2 ** 3;', self::asmInfixPower()],
            'Condition if' => ['if (true) { 10 }; 100;', self::asmConditionIf()],
            'Condition if else' => ['if (true) { 10 } else { 20 }; 100;', self::asmConditionIfElse()],
            'Binding let' => ['let foo = 2; let bar = foo; bar;', self::asmBindingLet()],
            'String' => ['"monkey";', self::asmString()],
            'String concat' => ['"mon" + "key";', self::asmStringConcat()],
            'Array empty' => ['[];', self::asmArrayEmpty()],
            'Array not empty' => ['[2, 3];', self::asmArrayNotEmpty()],
            'Array with expression' => ['[2 + 3, 5 + 6];', self::asmArrayWithExpression()],
            'Hash map empty' => ['{};', self::asmHashEmpty()],
            'Hash map simple' => ['{1: 2, 3: 4};', self::asmHashSimple()],
            'Hash map expression' => ['{1: 2 + 3, 4 + 5: 6};', self::asmHashExpression()],
            'Index array' => ['[1, 2, 3][1 + 1];', self::asmIndexArray()],
            'Index hash map' => ['{1 : 2}[2 - 1];', self::asmIndexHashMap()],
            'Function simple return' => ['fn() { return 5 + 10; };', self::asmFunctionSimpleReturn()],
            'Function implicit return' => ['fn() { 1; 2 };', self::asmFunctionImplicitReturn()],
            'Function with empty body' => ['fn() { };', self::asmFunctionWithEmptyBody()],
            'Function call simple' => ['fn() { 24 }();', self::asmFunctionCallSimple()],
            'Function call with let' => ['let foo = fn() { 24 }; foo();', self::asmFunctionCallWithLet()],
            'Function call nested' => [
                'let foo = fn() { 1 }; let bar = fn() { foo() + 2 }; bar();',
                self::asmFunctionCallNested()
            ],
            'Function call chained' => [
                'let foo = fn() { 2 }; let bar = fn() { foo }; bar()();',
                self::asmFunctionCallChained()
            ],
            'Local binding simple' => ['fn() { let x = 5; x }();', self::asmLocalBindingSimple()],
            'Local binding with global' => [
                'let globalSeed = 50; let minusOne = fn() { let num = 1; globalSeed - num }; let minusTwo = fn() { let num = 2; globalSeed - num }; minusOne() + minusTwo();',
                self::asmLocalBindingWithGlobal(),
            ],
            'Function call with one arg and empty body' => [
                'let foo = fn(a) { }; foo(42);',
                self::asmFunctionCallOneArgEmptyBody(),
            ],
            'Function call with two args' => [
                'let foo = fn(a, b) { a + b }; foo(2, 3);',
                self::asmFunctionCallTwoArgs(),
            ],
            'Function call with expression args' => [
                'let foo = fn(a, b) { a + b }; foo(2 + 3, 4);',
                self::asmFunctionCallExpressionArgs(),
            ],
            'Builtin len empty array' => ['len([]);', self::asmBuiltinLen()],
            'Builtin push' => ['push([], 1);', self::asmBuiltinPush()],
            'Builtin echo' => ['echo("foo");', self::asmBuiltinEcho()],
            'Builtin first' => ['first([1, 2]);', self::asmBuiltinFirst()],
            'Builtin last' => ['last([1, 2]);', self::asmBuiltinLast()],
            'Builtin rest' => ['rest([1, 2, 3]);', self::asmBuiltinRest()],
            'Closure newAdder' => [
                'let newAdder = fn(a, b) { fn(c) { a + b + c }; }; let adder = newAdder(1, 2); adder(8);',
                self::asmClosureNewAdder(),
            ],
            'Closure self-reference' => [
                'let wrapper = fn() { let self = fn() { self }; self }; wrapper();',
                self::asmClosureSelfReference(),
            ],
        ];
    }

    public static function asmOneValue(): string
    {
        return <<<'ASM'
; section .data
;    00000 42

Const 0
Pop

ASM;
    }

    public static function asmAllInfix(): string
    {
        return <<<'ASM'
; section .data
;    00000 2
;    00001 3
;    00002 4
;    00003 6

Const 0
Const 1
Add
Pop
Const 1
Const 2
Sub
Pop
Const 1
Const 2
Mul
Pop
Const 3
Const 1
Div
Pop

ASM;
    }

    public static function asmBoolean(): string
    {
        return <<<'ASM'
; section .data

True
Pop
False
Pop

ASM;
    }

    public static function asmComparaison(): string
    {
        return <<<'ASM'
; section .data
;    00000 2
;    00001 3

Const 0
Const 1
GreaterThan
Pop
Const 1
Const 0
GreaterThan
Pop
Const 0
Const 1
Equal
Pop
Const 0
Const 1
NotEqual
Pop

ASM;
    }

    public static function asmPrefixMinus(): string
    {
        return <<<'ASM'
; section .data
;    00000 2

Const 0
Minus
Pop

ASM;
    }

    public static function asmPrefixBang(): string
    {
        return <<<'ASM'
; section .data

True
Bang
Pop

ASM;
    }

    public static function asmPostfixFactorial(): string
    {
        return <<<'ASM'
; section .data
;    00000 3

Const 0
Factorial
Pop

ASM;
    }

    public static function asmInfixPower(): string
    {
        return <<<'ASM'
; section .data
;    00000 2
;    00001 3

Const 0
Const 1
Pow
Pop

ASM;
    }

    public static function asmConditionIf(): string
    {
        return <<<'ASM'
; section .data
;    00000 10
;    00001 100

True
JumpNotTruthy 10
Const 0
Jump 11
Unit
Pop
Const 1
Pop

ASM;
    }

    public static function asmConditionIfElse(): string
    {
        return <<<'ASM'
; section .data
;    00000 10
;    00001 20
;    00002 100

True
JumpNotTruthy 10
Const 0
Jump 13
Const 1
Pop
Const 2
Pop

ASM;
    }

    private static function asmBindingLet(): string
    {
        return <<<'ASM'
; section .data
;    00000 2

Const 0
SetGlobal 0
GetGlobal 0
SetGlobal 1
GetGlobal 1
Pop

ASM;
    }

    private static function asmString(): string
    {
        return <<<'ASM'
; section .data
;    00000 "monkey"

Const 0
Pop

ASM;
    }

    private static function asmStringConcat(): string
    {
        return <<<'ASM'
; section .data
;    00000 "mon"
;    00001 "key"

Const 0
Const 1
Add
Pop

ASM;
    }

    private static function asmArrayEmpty(): string
    {
        return <<<'ASM'
; section .data

Array 0
Pop

ASM;
    }

    private static function asmArrayNotEmpty(): string
    {
        return <<<'ASM'
; section .data
;    00000 2
;    00001 3

Const 0
Const 1
Array 2
Pop

ASM;
    }

    private static function asmArrayWithExpression(): string
    {
        return <<<'ASM'
; section .data
;    00000 2
;    00001 3
;    00002 5
;    00003 6

Const 0
Const 1
Add
Const 2
Const 3
Add
Array 2
Pop

ASM;
    }

    private static function asmHashEmpty(): string
    {
        return <<<'ASM'
; section .data

HashMap 0
Pop

ASM;
    }

    private static function asmHashSimple(): string
    {
        return <<<'ASM'
; section .data
;    00000 1
;    00001 2
;    00002 3
;    00003 4

Const 0
Const 1
Const 2
Const 3
HashMap 4
Pop

ASM;
    }

    private static function asmHashExpression(): string
    {
        return <<<'ASM'
; section .data
;    00000 1
;    00001 2
;    00002 3
;    00003 4
;    00004 5
;    00005 6

Const 0
Const 1
Const 2
Add
Const 3
Const 4
Add
Const 5
HashMap 4
Pop

ASM;
    }

    private static function asmIndexArray(): string
    {
        return <<<'ASM'
; section .data
;    00000 1
;    00001 2
;    00002 3

Const 0
Const 1
Const 2
Array 3
Const 0
Const 0
Add
Index
Pop

ASM;
    }

    private static function asmIndexHashMap(): string
    {
        return <<<'ASM'
; section .data
;    00000 1
;    00001 2

Const 0
Const 1
HashMap 2
Const 1
Const 0
Sub
Index
Pop

ASM;
    }

    private static function asmFunctionSimpleReturn(): string
    {
        return <<<'ASM'
; section .data
;    00000 5
;    00001 10
;    00002 @fn_0

fn_0:
    Const 0
    Const 1
    Add
    ReturnValue

Closure 2 0
Pop

ASM;
    }

    private static function asmFunctionImplicitReturn(): string
    {
        return <<<'ASM'
; section .data
;    00000 1
;    00001 2
;    00002 @fn_0

fn_0:
    Const 0
    Pop
    Const 1
    ReturnValue

Closure 2 0
Pop

ASM;
    }

    private static function asmFunctionWithEmptyBody(): string
    {
        return <<<'ASM'
; section .data
;    00000 @fn_0

fn_0:
    Return

Closure 0 0
Pop

ASM;
    }

    private static function asmFunctionCallSimple(): string
    {
        return <<<'ASM'
; section .data
;    00000 24
;    00001 @fn_0

fn_0:
    Const 0
    ReturnValue

Closure 1 0
Call 0
Pop

ASM;
    }

    private static function asmFunctionCallWithLet(): string
    {
        return <<<'ASM'
; section .data
;    00000 24
;    00001 @fn_0

fn_0:
    Const 0
    ReturnValue

Closure 1 0
SetGlobal 0
GetGlobal 0
Call 0
Pop

ASM;
    }

    private static function asmFunctionCallNested(): string
    {
        return <<<'ASM'
; section .data
;    00000 1
;    00001 @fn_0
;    00002 2
;    00003 @fn_1

fn_0:
    Const 0
    ReturnValue

fn_1:
    GetGlobal 0
    Call 0
    Const 2
    Add
    ReturnValue

Closure 1 0
SetGlobal 0
Closure 3 0
SetGlobal 1
GetGlobal 1
Call 0
Pop

ASM;
    }

    private static function asmFunctionCallChained(): string
    {
        return <<<'ASM'
; section .data
;    00000 2
;    00001 @fn_0
;    00002 @fn_1

fn_0:
    Const 0
    ReturnValue

fn_1:
    GetGlobal 0
    ReturnValue

Closure 1 0
SetGlobal 0
Closure 2 0
SetGlobal 1
GetGlobal 1
Call 0
Call 0
Pop

ASM;
    }

    private static function asmLocalBindingSimple(): string
    {
        return <<<'ASM'
; section .data
;    00000 5
;    00001 @fn_0

fn_0:
    Const 0
    SetLocal 0
    GetLocal 0
    ReturnValue

Closure 1 0
Call 0
Pop

ASM;
    }

    private static function asmLocalBindingWithGlobal(): string
    {
        return <<<'ASM'
; section .data
;    00000 50
;    00001 1
;    00002 @fn_0
;    00003 2
;    00004 @fn_1

fn_0:
    Const 1
    SetLocal 0
    GetGlobal 0
    GetLocal 0
    Sub
    ReturnValue

fn_1:
    Const 3
    SetLocal 0
    GetGlobal 0
    GetLocal 0
    Sub
    ReturnValue

Const 0
SetGlobal 0
Closure 2 0
SetGlobal 1
Closure 4 0
SetGlobal 2
GetGlobal 1
Call 0
GetGlobal 2
Call 0
Add
Pop

ASM;
    }

    private static function asmFunctionCallOneArgEmptyBody(): string
    {
        return <<<'ASM'
; section .data
;    00000 @fn_0
;    00001 42

fn_0:
    Return

Closure 0 0
SetGlobal 0
Const 1
GetGlobal 0
Call 1
Pop

ASM;
    }

    private static function asmFunctionCallTwoArgs(): string
    {
        return <<<'ASM'
; section .data
;    00000 @fn_0
;    00001 2
;    00002 3

fn_0:
    GetLocal 0
    GetLocal 1
    Add
    ReturnValue

Closure 0 0
SetGlobal 0
Const 1
Const 2
GetGlobal 0
Call 2
Pop

ASM;
    }

    private static function asmFunctionCallExpressionArgs(): string
    {
        return <<<'ASM'
; section .data
;    00000 @fn_0
;    00001 2
;    00002 3
;    00003 4

fn_0:
    GetLocal 0
    GetLocal 1
    Add
    ReturnValue

Closure 0 0
SetGlobal 0
Const 1
Const 2
Add
Const 3
GetGlobal 0
Call 2
Pop

ASM;
    }

    private static function asmBuiltinLen(): string
    {
        return <<<'ASM'
; section .data

Array 0
GetBuiltin len
Call 1
Pop

ASM;
    }

    private static function asmBuiltinPush(): string
    {
        return <<<'ASM'
; section .data
;    00000 1

Array 0
Const 0
GetBuiltin push
Call 2
Pop

ASM;
    }

    private static function asmBuiltinEcho(): string
    {
        return <<<'ASM'
; section .data
;    00000 "foo"

Const 0
GetBuiltin echo
Call 1
Pop

ASM;
    }

    private static function asmBuiltinFirst(): string
    {
        return <<<'ASM'
; section .data
;    00000 1
;    00001 2

Const 0
Const 1
Array 2
GetBuiltin first
Call 1
Pop

ASM;
    }

    private static function asmBuiltinLast(): string
    {
        return <<<'ASM'
; section .data
;    00000 1
;    00001 2

Const 0
Const 1
Array 2
GetBuiltin last
Call 1
Pop

ASM;
    }

    private static function asmBuiltinRest(): string
    {
        return <<<'ASM'
; section .data
;    00000 1
;    00001 2
;    00002 3

Const 0
Const 1
Const 2
Array 3
GetBuiltin rest
Call 1
Pop

ASM;
    }

    private static function asmClosureNewAdder(): string
    {
        return <<<'ASM'
; section .data
;    00000 @fn_0
;    00001 @fn_1
;    00002 1
;    00003 2
;    00004 8

fn_0:
    GetFree 0
    GetFree 1
    Add
    GetLocal 0
    Add
    ReturnValue

fn_1:
    GetLocal 0
    GetLocal 1
    Closure 0 2
    ReturnValue

Closure 1 0
SetGlobal 0
Const 2
Const 3
GetGlobal 0
Call 2
SetGlobal 1
Const 4
GetGlobal 1
Call 1
Pop

ASM;
    }

    private static function asmClosureSelfReference(): string
    {
        return <<<'ASM'
; section .data
;    00000 @fn_0
;    00001 @fn_1

fn_0:
    CurrentClosure
    ReturnValue

fn_1:
    Closure 0 0
    SetLocal 0
    GetLocal 0
    ReturnValue

Closure 1 0
SetGlobal 0
GetGlobal 0
Call 0
Pop

ASM;
    }
}
