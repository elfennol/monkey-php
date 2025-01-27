<?php

declare(strict_types=1);

namespace Elfennol\MonkeyPhp\Token\Debug;

use JsonSerializable;

/**
 * @internal
 */
readonly class Debug implements JsonSerializable
{
    public function __construct(public int $line, public int $col)
    {
    }

    /**
     * @return array{line: int, col: int}
     */
    public function jsonSerialize(): array
    {
        return [
            'line' => $this->line,
            'col' => $this->col,
        ];
    }
}
