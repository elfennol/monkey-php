<?php

declare(strict_types=1);

namespace Elfennol\MonkeyPhp\Utils\String;

use Iterator;

/**
 * @implements Iterator<int, string>
 */
class StringIterator implements Iterator
{
    private int $position;
    private string $char;

    public function __construct(
        private readonly string $input,
        private readonly StringUtils $stringUtils,
    ) {
    }

    public function current(): string
    {
        return $this->char;
    }

    public function next(): void
    {
        $this->char = $this->position >= $this->stringUtils->len($this->input)
            ? "\0"
            : $this->stringUtils->char($this->input, $this->position);
        ++$this->position;
    }

    public function key(): int
    {
        return $this->position;
    }

    public function valid(): bool
    {
        return "\0" !== $this->char;
    }

    public function rewind(): void
    {
        $this->position = 0;
        $this->next();
    }
}
