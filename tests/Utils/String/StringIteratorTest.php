<?php

declare(strict_types=1);

namespace Elfennol\MonkeyPhp\Tests\Utils\String;

use Elfennol\MonkeyPhp\Utils\String\StringBuilder;
use Elfennol\MonkeyPhp\Utils\String\StringUtils;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class StringIteratorTest extends TestCase
{
    #[DataProvider('stringProvider')]
    public function testStringIterator(string $input): void
    {
        $stringBuilder = new StringBuilder(new StringUtils());
        $stringIterator = $stringBuilder->build($input);

        $actualString = [];
        $position = 0;
        foreach ($stringIterator as $key => $char) {
            $actualString[] = $char;
            self::assertSame(++$position, $key);
        }

        self::assertSame($input, implode('', $actualString));
    }

    /**
     * @return string[][]
     */
    public static function stringProvider(): array
    {
        return [
            ['qwerty'],
            [''],
        ];
    }
}
