<?php

declare(strict_types=1);

namespace Elfennol\MonkeyPhp\Tests\Utils\Option;

use Elfennol\MonkeyPhp\Utils\Option\None;
use Elfennol\MonkeyPhp\Utils\Option\Some;
use Exception;
use PHPUnit\Framework\TestCase;

class OptionTest extends TestCase
{
    public function testSome(): void
    {
        $some = new Some('foo');
        self::assertTrue($some->isSome());
        self::assertFalse($some->isNone());
        self::assertSame('foo', $some->unwrap());
        self::assertSame('foo', $some->unwrapOr('bar'));
    }

    public function testNone(): void
    {
        $none = new None();
        self::assertFalse($none->isSome());
        self::assertTrue($none->isNone());
        self::assertSame('bar', $none->unwrapOr('bar'));
    }

    public function testNoneUnwrap(): void
    {
        $this->expectException(Exception::class);
        (new None())->unwrap();
    }
}
