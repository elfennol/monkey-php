<?php

declare(strict_types=1);

namespace Elfennol\MonkeyPhp\Tests\Vm;

use Elfennol\MonkeyPhp\SysObject\SysObjectInterface;
use Elfennol\MonkeyPhp\Vm\Globals;
use Elfennol\MonkeyPhp\Vm\VmException;
use PHPUnit\Framework\TestCase;

class GlobalsTest extends TestCase
{
    private Globals $globals;

    protected function setUp(): void
    {
        $this->globals = new Globals();
    }

    public function testOffsetSetAndGet(): void
    {
        $element = $this->createStub(SysObjectInterface::class);

        $this->globals[0] = $element;

        self::assertSame($element, $this->globals[0]);
    }

    public function testOffsetExistsFalse(): void
    {
        $globals = new Globals();

        self::assertFalse(isset($globals[0]));
    }

    public function testOffsetExistsTrue(): void
    {
        $element = $this->createStub(SysObjectInterface::class);

        $this->globals[0] = $element;

        /** @phpstan-ignore isset.initializedProperty */
        self::assertTrue(isset($this->globals[0]));
    }

    public function testOffsetUnset(): void
    {
        $element = $this->createStub(SysObjectInterface::class);

        $this->globals[0] = $element;
        unset($this->globals[0]);

        self::assertFalse(isset($this->globals[0]));
    }

    public function testOffsetGetThrowsOnUnknownIndex(): void
    {
        $this->expectException(VmException::class);

        /** @phpstan-ignore expr.resultUnused */
        $this->globals[42];
    }

    public function testMultipleElements(): void
    {
        $element1 = $this->createStub(SysObjectInterface::class);
        $element2 = $this->createStub(SysObjectInterface::class);

        $this->globals[0] = $element1;
        $this->globals[1] = $element2;

        self::assertSame($element1, $this->globals[0]);
        self::assertSame($element2, $this->globals[1]);
    }
}
