<?php

declare(strict_types=1);

namespace Elfennol\MonkeyPhp\Utils\Type;

use Exception;

readonly class NumberHelper
{
    /**
     * @return Byte[]
     *
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function toByteFixedArray(int $value, int $width): array
    {
        $totalBytes = $this->totalBytes($value);

        if ($width < $totalBytes) {
            throw new Exception('Overflow.');
        }

        $offset = ($totalBytes - 1) * 8;
        $bytes = [];
        $index = 0;
        $bytes[$index] = Byte::fromInt(($value >> $offset) & 0xFF);
        while (0 !== $offset) {
            $offset -= 8;
            $index++;
            $bytes[$index] = Byte::fromInt(($value >> $offset) & 0xFF);
        }

        /** @var Byte[] $bytes */
        $bytes = array_pad($bytes, -$width, Byte::fromInt(0));

        return $bytes;
    }

    private function totalBytes(int $value): int
    {
        $totalBytes = 0;
        while (0 !== $value) {
            $value >>= 8;
            $totalBytes++;
        }

        return $totalBytes;
    }
}
