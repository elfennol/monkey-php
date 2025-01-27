<?php

declare(strict_types=1);

namespace Elfennol\MonkeyPhp\Utils\Option;

/**
 * @template T
 */
interface Option
{
    public function isSome(): bool;

    public function isNone(): bool;

    /**
     * @return T
     */
    public function unwrap(): mixed;

    /**
     * @param T $default
     *
     * @return T
     */
    public function unwrapOr(mixed $default): mixed;
}
