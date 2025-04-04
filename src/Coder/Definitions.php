<?php

declare(strict_types=1);

namespace Elfennol\MonkeyPhp\Coder;

readonly class Definitions
{
    /**
     * @var array<int, Definition>
     */
    private array $definitions;

    public function __construct()
    {
        $this->definitions = [
            OpCode::Const->value => new Definition(OpCode::Const, [2]),
        ];
    }

    /**
     * @return array<int, Definition>
     */
    public function get(): array
    {
        return $this->definitions;
    }
}
