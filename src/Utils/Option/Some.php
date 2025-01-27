<?php

declare(strict_types=1);

namespace Elfennol\MonkeyPhp\Utils\Option;

/**
 * @template T
 *
 * @implements Option<T>
 */
readonly class Some implements Option
{
    /**
     * @param T $value
     */
    public function __construct(
        private mixed $value
    ) {
    }

    public function isSome(): bool
    {
        return true;
    }

    public function isNone(): bool
    {
        return false;
    }

    public function unwrap(): mixed
    {
        return $this->value;
    }

    /**
     * {@inheritdoc}
     */
    public function unwrapOr(mixed $default): mixed
    {
        return $this->unwrap();
    }
}
