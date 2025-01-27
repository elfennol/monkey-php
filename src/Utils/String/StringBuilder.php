<?php

declare(strict_types=1);

namespace Elfennol\MonkeyPhp\Utils\String;

readonly class StringBuilder
{
    public function __construct(
        private StringUtils $stringUtils,
    ) {
    }

    public function build(string $input): StringIterator
    {
        return new StringIterator($input, $this->stringUtils);
    }
}
