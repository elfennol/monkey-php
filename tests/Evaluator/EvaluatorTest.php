<?php

declare(strict_types=1);

namespace Elfennol\MonkeyPhp\Tests\Evaluator;

use Elfennol\MonkeyPhp\Evaluator\EvaluatorExceptionType;
use Elfennol\MonkeyPhp\SysObject\Catalog\ArraySysObject;
use Elfennol\MonkeyPhp\SysObject\Catalog\BoolSysObject;
use Elfennol\MonkeyPhp\SysObject\Catalog\HashMapSysObject;
use Elfennol\MonkeyPhp\SysObject\Catalog\IntSysObject;
use Elfennol\MonkeyPhp\SysObject\Catalog\StringSysObject;
use Elfennol\MonkeyPhp\SysObject\Catalog\UnitSysObject;
use Elfennol\MonkeyPhp\Tests\EvaluatorAssertionTrait;
use Elfennol\MonkeyPhp\Tests\EvaluatorFactoryTrait;
use Elfennol\MonkeyPhp\Tests\LexerFactoryTrait;
use Elfennol\MonkeyPhp\Tests\ParserFactoryTrait;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 * @SuppressWarnings(PHPMD.TooManyMethods) Maybe organize by sub-evaluator.
 */
class EvaluatorTest extends TestCase
{
    use LexerFactoryTrait;
    use EvaluatorAssertionTrait;
    use EvaluatorFactoryTrait;
    use ParserFactoryTrait;

    #[DataProvider('evaluatorProvider')]
    public function testEvaluator(string $input, ?string $expectedValue, string $expectedObject): void
    {
        $this->assertEvaluator($input, $expectedValue, $expectedObject);
    }

    #[DataProvider('assignProvider')]
    public function testAssign(string $input, ?string $expectedValue, string $expectedObject): void
    {
        $this->assertEvaluator($input, $expectedValue, $expectedObject);
    }

    #[DataProvider('builtinProvider')]
    public function testBuiltin(string $input, ?string $expectedValue, string $expectedObject): void
    {
        $this->assertEvaluator($input, $expectedValue, $expectedObject);
    }

    public function testEchoBuiltin(): void
    {
        $this->assertEvaluator('echo("foo");', 'foo', UnitSysObject::class);
        $this->expectOutputString('foo');
    }

    #[DataProvider('arrayProvider')]
    public function testArray(string $input, ?string $expectedValue, string $expectedObject): void
    {
        $this->assertEvaluator($input, $expectedValue, $expectedObject);
    }

    #[DataProvider('indexProvider')]
    public function testIndex(string $input, ?string $expectedValue, string $expectedObject): void
    {
        $this->assertEvaluator($input, $expectedValue, $expectedObject);
    }

    #[DataProvider('hashMapProvider')]
    public function testHashMap(string $input, ?string $expectedValue, string $expectedObject): void
    {
        $this->assertEvaluator($input, $expectedValue, $expectedObject);
    }

    public function testExampleMapFn(): void
    {
        $input = <<<MAPFN
let map = fn(arr, f) {
    let iter = fn(arr, accumulated) {
        if (len(arr) == 0) {
            accumulated
        } else {
            iter(rest(arr), push(accumulated, f(first(arr))));
        }
    };
    
    iter(arr, []);
};

let a = [1, 2, 3, 4];
let double = fn(x) { x * 2 };
map(a, double);
MAPFN;

        $this->assertEvaluator($input, '[2, 4, 6, 8]', ArraySysObject::class);
    }

    public function testExampleReduceFn(): void
    {
        $input = <<<REDUCEFN
let reduce = fn(arr, initial, f) {
    let iter = fn(arr, result) {
        if (len(arr) == 0) {
            result
        } else {
            iter(rest(arr), f(result, first(arr)));
        }
    };
    
    iter(arr, initial);
};

let sum = fn(arr) {
    reduce(arr, 0, fn(initial, el) { initial + el });
};
sum([1, 2, 3, 4, 5]);
REDUCEFN;

        $this->assertEvaluator($input, '15', IntSysObject::class);
    }

    #[DataProvider('evaluatorErrorProvider')]
    public function testEvaluatorWhenError(
        string $input,
        EvaluatorExceptionType $exceptionType,
        string $exceptionMsg,
    ): void {
        $this->assertEvaluatorException($input, $exceptionType, $exceptionMsg);
    }

    #[DataProvider('assignErrorProvider')]
    public function testAssignWhenError(
        string $input,
        EvaluatorExceptionType $exceptionType,
        string $exceptionMsg
    ): void {
        $this->assertEvaluatorException($input, $exceptionType, $exceptionMsg);
    }

    #[DataProvider('builtinErrorProvider')]
    public function testBuiltinWhenError(
        string $input,
        EvaluatorExceptionType $exceptionType,
        string $exceptionMsg
    ): void {
        $this->assertEvaluatorException($input, $exceptionType, $exceptionMsg);
    }

    #[DataProvider('arrayErrorProvider')]
    public function testArrayAndHashMapWithError(
        string $input,
        EvaluatorExceptionType $exceptionType,
        string $exceptionMsg
    ): void {
        $this->assertEvaluatorException($input, $exceptionType, $exceptionMsg);
    }

    #[DataProvider('indexErrorProvider')]
    public function testIndexWithError(
        string $input,
        EvaluatorExceptionType $exceptionType,
        string $exceptionMsg
    ): void {
        $this->assertEvaluatorException($input, $exceptionType, $exceptionMsg);
    }

    /**
     * @return array<int, array<string|null>>
     */
    public static function evaluatorProvider(): array
    {
        return [
            ['', null, UnitSysObject::class],

            ['42;', '42', IntSysObject::class],
            ['true;', 'true', BoolSysObject::class],
            ['false;', 'false', BoolSysObject::class],
            ['"foo";', 'foo', StringSysObject::class],

            ['!true;', 'false', BoolSysObject::class],
            ['!!true;', 'true', BoolSysObject::class],
            ['!false;', 'true', BoolSysObject::class],
            ['!!false;', 'false', BoolSysObject::class],

            ['-1;', '-1', IntSysObject::class],
            ['--1;', '1', IntSysObject::class],
            ['+1;', '1', IntSysObject::class],
            ['++1;', '1', IntSysObject::class],
            ['-+1;', '-1', IntSysObject::class],
            ['+-1;', '-1', IntSysObject::class],
            ['2+3;', '5', IntSysObject::class],
            ['2-3;', '-1', IntSysObject::class],
            ['2*3;', '6', IntSysObject::class],
            ['10/3;', '3', IntSysObject::class],
            ['1 + 12 * (2 + 3) - 100 / 2 + -4;', '7', IntSysObject::class],

            ['"foo" + "bar";', 'foobar', StringSysObject::class],
            ['"foo" == "foo";', 'true', BoolSysObject::class],
            ['"foo" == "bar";', 'false', BoolSysObject::class],
            ['"foo" != "bar";', 'true', BoolSysObject::class],
            ['"foo" != "foo";', 'false', BoolSysObject::class],

            ['1 < 2;', 'true', BoolSysObject::class],
            ['1 > 2;', 'false', BoolSysObject::class],
            ['2 < 1;', 'false', BoolSysObject::class],
            ['2 > 1;', 'true', BoolSysObject::class],
            ['1 == 1;', 'true', BoolSysObject::class],
            ['2 == 1;', 'false', BoolSysObject::class],
            ['1 != 1;', 'false', BoolSysObject::class],
            ['2 != 1;', 'true', BoolSysObject::class],

            ['true == true;', 'true', BoolSysObject::class],
            ['true == false;', 'false', BoolSysObject::class],
            ['true != true;', 'false', BoolSysObject::class],
            ['true != false;', 'true', BoolSysObject::class],
            ['(1 < 2) == true;', 'true', BoolSysObject::class],
            ['(1 > 2) == true;', 'false', BoolSysObject::class],

            ['if (1 < 2) { 10 } else { 20 };', '10', IntSysObject::class],
            ['if (1 > 2) { 10 } else { 20 };', '20', IntSysObject::class],
            ['if (1 < 2) { 10 };', '10', IntSysObject::class],
            ['if (1 > 2) { 10 };', null, UnitSysObject::class],
            ['if (1 > 2) { 10 } else { };', null, UnitSysObject::class],

            ['return 42;', '42', IntSysObject::class],
            ['return;', null, UnitSysObject::class],
            ['41; return 42;', '42', IntSysObject::class],
            ['41; return 42; 43;', '42', IntSysObject::class],
            ['if (10 > 1) { if (10 > 1) { return 10; };  return 1; };', '10', IntSysObject::class],
            ['if (10 > 1) { return 10; }; return 1;', '10', IntSysObject::class],

            ['let add = fn (x, y) { x + y }; add(1, 2);', '3', IntSysObject::class],
            ['let add = fn (x, y) { return x + y; }; add(1, 2);', '3', IntSysObject::class],
            ['let add = fn (x, y) { return x + y; }; add(1, add(2, 3));', '6', IntSysObject::class],
            ['fn (x, y) { return x + y; }(1, 2);', '3', IntSysObject::class],
            ['let x = 1; let add = fn (x, y) { x + y }; add(2, 3);', '5', IntSysObject::class],
            ['let x = 1; let add = fn (x, y) { x + y }; add(2, 3); x;', '1', IntSysObject::class],
            [
                'let newAdder = fn(x) { fn(y) { x + y } }; let addTwo = newAdder(2); addTwo(1);',
                '3',
                IntSysObject::class
            ],
            [
                'let factorial = fn(n) { if (n == 0) { 1 } else { n * factorial(n-1)} }; factorial(5);',
                '120',
                IntSysObject::class
            ],

            ['5!;', '120', IntSysObject::class],
            ['-5!;', '-120', IntSysObject::class],
            ['2**3;', '8', IntSysObject::class],
            ['-2**2;', '-4', IntSysObject::class],
            ['(-2)**2;', '4', IntSysObject::class],
            ['3*2**3*4;', '96', IntSysObject::class],
        ];
    }

    /**
     * @return array<int, array<string>>
     */
    public static function assignProvider(): array
    {
        return [
            ['let a = 5; a;', '5', IntSysObject::class],
            ['let a = 5 * 5; a;', '25', IntSysObject::class],
            ['let a = 5; let b = a; b;', '5', IntSysObject::class],
            ['let a = 5; let b = a; let c = a + b + 5; c;', '15', IntSysObject::class],
            ['let a = 5; a = 4; a;', '4', IntSysObject::class],
        ];
    }

    /**
     * @return array<int, array<string>>
     */
    public static function arrayProvider(): array
    {
        return [
            ['[];', '[]', ArraySysObject::class],
            ['[1];', '[1]', ArraySysObject::class],
            ['[1, 2];', '[1, 2]', ArraySysObject::class],
        ];
    }

    /**
     * @return array<int, array<string>>
     */
    public static function indexProvider(): array
    {
        return [
            ['[1][0];', '1', IntSysObject::class],
            ['[1, 2, 3][1];', '2', IntSysObject::class],
            ['let foo = [1, 2, 3]; foo[1];', '2', IntSysObject::class],
            ['let foo = fn () {[1, 2, 3]}; foo()[1];', '2', IntSysObject::class],
            ['{ "foo": "bar" }["foo"];', 'bar', StringSysObject::class],
            ['let var = { "foo": "bar" }; var["foo"];', 'bar', StringSysObject::class],
        ];
    }

    /**
     * @return array<int, array<string>>
     */
    public static function hashMapProvider(): array
    {
        return [
            ['{};', '{}', HashMapSysObject::class],
            ['{"foo": "bar"};', '{"foo": "bar"}', HashMapSysObject::class],
            ['{"foo": 1+2};', '{"foo": 3}', HashMapSysObject::class],
            [
                '{"foo": "bar", true: "bool", 1: "int"};',
                '{"foo": "bar", true: "bool", 1: "int"}',
                HashMapSysObject::class
            ],
            [
                '{"foo"+"baz": "bar", !true: "bool", 1 + 2: "int"};',
                '{"foobaz": "bar", false: "bool", 3: "int"}',
                HashMapSysObject::class
            ],
        ];
    }

    /**
     * @return array<int, array<string|null>>
     */
    public static function builtinProvider(): array
    {
        return [
            ['first([0, 1, 2]);', '0', IntSysObject::class],
            ['last([0, 1, 2]);', '2', IntSysObject::class],
            ['len("Hello");', '5', IntSysObject::class],
            ['len([0, 1, 2]);', '3', IntSysObject::class],
            ['push([0, 1, 2], 3);', '[0, 1, 2, 3]', ArraySysObject::class],
            ['rest([]);', '[]', ArraySysObject::class],
            ['rest([0, 1, 2]);', '[1, 2]', ArraySysObject::class],
        ];
    }

    /**
     * @return array<int, array<string|EvaluatorExceptionType>>
     */
    public static function evaluatorErrorProvider(): array
    {
        return [
            ['-true;', EvaluatorExceptionType::PrefixOpNotSupported, 'Unable to evaluate prefix op node.'],
            ['+true;', EvaluatorExceptionType::PrefixOpNotSupported, 'Unable to evaluate prefix op node.'],
            ['!1;', EvaluatorExceptionType::PrefixOpNotSupported, 'Unable to evaluate prefix op node.'],
            ['if (1) { 2 };', EvaluatorExceptionType::ConditionInvalid, 'Condition must be boolean.'],
            ['true + true;', EvaluatorExceptionType::InfixOpNotSupported, 'Unable to evaluate infix op node.'],
            ['"foo" - "bar";', EvaluatorExceptionType::InfixOpNotSupported, 'Unable to evaluate infix op node.'],
            [
                '2**-1;',
                EvaluatorExceptionType::InfixOpNotSupported,
                'Unable to evaluate infix op node. Operand must be positive.'
            ],
            ['let a = 1; a(2);', EvaluatorExceptionType::ExprNotCallable, 'Unable to evaluate FnCall node.'],
            [
                '(-5)!;',
                EvaluatorExceptionType::OperandInvalid,
                'Unable to evaluate postfix op node.'
            ],
            ['true!;', EvaluatorExceptionType::PostfixOpNotSupported, 'Unable to evaluate postfix op node.'],
        ];
    }

    /**
     * @return array<int, array<string|EvaluatorExceptionType>>
     */
    public static function assignErrorProvider(): array
    {
        return [
            ['let a = 5; b;', EvaluatorExceptionType::ContextIdentifierNotFound, 'Unable to evaluate identifier node.'],
            ['let a = 1; let a = 2;', EvaluatorExceptionType::ContextIdentifierConflict, 'Identifier already defined.'],
            ['a = 1;', EvaluatorExceptionType::ContextIdentifierUndefined, 'Identifier undefined.'],
            ['let len = 1;', EvaluatorExceptionType::ContextIdentifierConflict, 'Identifier already defined.'],
        ];
    }

    /**
     * @return array<int, array<string|EvaluatorExceptionType>>
     */
    public static function builtinErrorProvider(): array
    {
        return [
            ['first([]);', EvaluatorExceptionType::EmptySysObject, ''],
            ['last([]);', EvaluatorExceptionType::EmptySysObject, ''],
        ];
    }

    /**
     * @return array<int, array<string|EvaluatorExceptionType>>
     */
    public static function arrayErrorProvider(): array
    {
        return [
            [
                '[fn() {42}];',
                EvaluatorExceptionType::NodeInvalid,
                'Unable to evaluate array node: elements must be atom.'
            ],
            [
                '{"foo": fn() {42}};',
                EvaluatorExceptionType::NodeInvalid,
                'Unable to evaluate hashmap value node: elements must be atom.'
            ],
            [
                '{fn() {42}: "bar"};',
                EvaluatorExceptionType::NodeInvalid,
                'Unable to evaluate hashmap key node: elements must be atom.'
            ],
        ];
    }

    /**
     * @return array<int, array<string|EvaluatorExceptionType>>
     */
    public static function indexErrorProvider(): array
    {
        return [
            ['[1, 2][-1];', EvaluatorExceptionType::WrongIndex, 'Index must be positive.'],
            ['[1, 2][2];', EvaluatorExceptionType::WrongIndex, 'Index too big.'],
            ['{ "foo": "bar"}["baz"];', EvaluatorExceptionType::WrongIndex, 'Index not available.'],
        ];
    }
}
