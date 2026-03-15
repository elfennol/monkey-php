<?php

declare(strict_types=1);

namespace Elfennol\MonkeyPhp\Utils\Type;

interface Hashable
{
    public function hash(): string;
}
