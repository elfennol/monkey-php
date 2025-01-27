<?php

declare(strict_types=1);

namespace Elfennol\MonkeyPhp\Utils\String;

class StringUtils
{
    private const ENCODING = 'UTF-8';

    public function len(string $input): int
    {
        return mb_strlen($input, self::ENCODING);
    }

    public function char(string $input, int $position): string
    {
        return mb_substr($input, $position, 1, self::ENCODING);
    }
}
