<?php

declare(strict_types=1);

namespace Elfennol\MonkeyPhp\Utils\Option;

use Exception;

/**
 * @implements Option<mixed>
 */
readonly class None implements Option
{
    public function isSome(): bool
    {
        return false;
    }

    public function isNone(): bool
    {
        return true;
    }

    public function unwrap(): never
    {
        throw new Exception('Attempt to call unwrap() on None value');
    }

    public function unwrapOr(mixed $default): mixed
    {
        return $default;
    }
}
