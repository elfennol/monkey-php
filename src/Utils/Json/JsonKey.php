<?php

declare(strict_types=1);

namespace Elfennol\MonkeyPhp\Utils\Json;

/**
 * Key value is prefixed to force associative array and avoid this following behavior (0 omitted):
 *     var_dump(json_encode([0 => []])); // string(4) "[[]]"
 *     var_dump(json_encode([1 => []])); // string(8) "{"1":[]}"
 */
readonly class JsonKey
{
    public string $key;

    public function __construct(private string $value)
    {
        $this->key = sprintf(':%s', $this->value);
    }
}
