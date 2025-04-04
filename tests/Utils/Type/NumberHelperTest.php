<?php

declare(strict_types=1);

namespace Elfennol\MonkeyPhp\Tests\Utils\Type;

use Elfennol\MonkeyPhp\Utils\Type\Byte;
use Elfennol\MonkeyPhp\Utils\Type\NumberHelper;
use Exception;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class NumberHelperTest extends TestCase
{
    private NumberHelper $numberHelper;

    protected function setUp(): void
    {
        $this->numberHelper = new NumberHelper();
    }

    /**
     * @param Byte[] $expected
     */
    #[DataProvider('toByteFixedArrayProvider')]
    public function testToByteFixedArray(int $input, int $width, array $expected): void
    {
        $bytes = $this->numberHelper->toByteFixedArray($input, $width);

        $index = 0;
        foreach ($bytes as $byte) {
            self::assertInstanceOf(Byte::class, $byte);
            self::assertSame($expected[$index]->toInt(), $byte->toInt());
            $index++;
        }
    }

    public function testToByteFixedArrayWhenOverflow(): void
    {
        $this->expectException(Exception::class);

        $this->numberHelper->toByteFixedArray(65534, 1);
    }

    /**
     * @return array<string, array{int, int, Byte[]}>
     *
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public static function toByteFixedArrayProvider(): array
    {
        return [
            'Input with a 2 bytes and width 2' => [65534, 2, [Byte::fromInt(255), Byte::fromInt(254)]],
            'Input with a 1 byte and width 2' => [254, 2, [Byte::fromInt(0), Byte::fromInt(254)]],
        ];
    }
}
