<?php

declare(strict_types=1);

namespace Elfennol\MonkeyPhp\Compiler\Symbol;

readonly class Symbol
{
    public function __construct(public string $name, public Scope $scope, public int $index)
    {
    }
}
