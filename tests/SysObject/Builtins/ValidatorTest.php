<?php

declare(strict_types=1);

namespace Elfennol\MonkeyPhp\Tests\SysObject\Builtins;

use Elfennol\MonkeyPhp\SysObject\Builtins\BuiltinException;
use Elfennol\MonkeyPhp\SysObject\Builtins\BuiltinExceptionType;
use Elfennol\MonkeyPhp\SysObject\Builtins\BuiltinName;
use Elfennol\MonkeyPhp\SysObject\Builtins\Validator;
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
            self::fail('Expected BuiltinException');
        } catch (BuiltinException $exception) {
            self::assertSame(BuiltinExceptionType::FnWrongArgsNumber, $exception->getType());
            self::assertArrayHasKey('name', $exception->getContext());
            self::assertSame(BuiltinName::Echo->value, $exception->getContext()['name']);
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
            self::fail('Expected BuiltinException');
        } catch (BuiltinException $exception) {
            self::assertSame(BuiltinExceptionType::FnWrongArgType, $exception->getType());
            self::assertArrayHasKey('name', $exception->getContext());
            self::assertSame(BuiltinName::Echo->value, $exception->getContext()['name']);
        }
    }

    public function testThrowFnWrongArgType(): void
    {
        try {
            $this->validator->throwFnWrongArgType(BuiltinName::Echo, new StringSysObject('foo'));
            /** @phpstan-ignore deadCode.unreachable */
            self::fail('Expected BuiltinException');
        } catch (BuiltinException $exception) {
            self::assertSame(BuiltinExceptionType::FnWrongArgType, $exception->getType());
            self::assertArrayHasKey('name', $exception->getContext());
            self::assertSame(BuiltinName::Echo->value, $exception->getContext()['name']);
        }
    }
}
