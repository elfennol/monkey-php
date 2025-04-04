<?php

declare(strict_types=1);

namespace Elfennol\MonkeyPhp\Tests\Utils\Type;

use Elfennol\MonkeyPhp\Utils\Type\Byte;
use Exception;
use PHPUnit\Framework\TestCase;

class ByteTest extends TestCase
{
    public function testByte(): void
    {
        self::assertSame(23, Byte::fromInt(23)->toInt());
    }

    /**
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function testByteWhenInvalidInput(): void
    {
        $this->expectException(Exception::class);

        Byte::fromInt(256);
    }
}
