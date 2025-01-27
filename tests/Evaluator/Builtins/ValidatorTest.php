<?php

declare(strict_types=1);

namespace Elfennol\MonkeyPhp\Tests\Evaluator\Builtins;

use Elfennol\MonkeyPhp\Evaluator\Builtins\BuiltinName;
use Elfennol\MonkeyPhp\Evaluator\Builtins\Validator;
use Elfennol\MonkeyPhp\Evaluator\EvaluatorException;
use Elfennol\MonkeyPhp\Evaluator\EvaluatorExceptionType;
use Elfennol\MonkeyPhp\SysObject\Catalog\StringSysObject;
use Elfennol\MonkeyPhp\SysObject\Catalog\UnitSysObject;
use PHPUnit\Framework\TestCase;

class ValidatorTest extends TestCase
{
    private Validator $validator;

    protected function setUp(): void
    {
        $this->validator = new Validator();
    }

    public function testAssertArgsNumber(): void
    {
        try {
            $this->validator->assertArgsNumber(
                BuiltinName::Echo,
                [new StringSysObject('foo'), new StringSysObject('bar')],
                1
            );
            self::fail('Expected EvaluatorException');
        } catch (EvaluatorException $evaluatorException) {
            self::assertSame(EvaluatorExceptionType::FnWrongArgsNumber, $evaluatorException->getType());
            self::assertArrayHasKey('name', $evaluatorException->getContext());
            self::assertSame(BuiltinName::Echo->value, $evaluatorException->getContext()['name']);
        }
    }

    public function testAssertArgsType(): void
    {
        try {
            $this->validator->assertArgsType(
                BuiltinName::Echo,
                new StringSysObject('foo'),
                UnitSysObject::class
            );
            self::fail('Expected EvaluatorException');
        } catch (EvaluatorException $evaluatorException) {
            self::assertSame(EvaluatorExceptionType::FnWrongArgType, $evaluatorException->getType());
            self::assertArrayHasKey('name', $evaluatorException->getContext());
            self::assertSame(BuiltinName::Echo->value, $evaluatorException->getContext()['name']);
        }
    }

    public function testThrowFnWrongArgType(): void
    {
        try {
            $this->validator->throwFnWrongArgType(BuiltinName::Echo, new StringSysObject('foo'));
            /** @phpstan-ignore deadCode.unreachable */
            self::fail('Expected EvaluatorException');
        } catch (EvaluatorException $evaluatorException) {
            self::assertSame(EvaluatorExceptionType::FnWrongArgType, $evaluatorException->getType());
            self::assertArrayHasKey('name', $evaluatorException->getContext());
            self::assertSame(BuiltinName::Echo->value, $evaluatorException->getContext()['name']);
        }
    }
}
