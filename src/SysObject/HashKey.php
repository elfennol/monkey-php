<?php

declare(strict_types=1);

namespace Elfennol\MonkeyPhp\SysObject;

readonly class HashKey
{
    public function hash(AtomSysObjectInterface $key): string
    {
        return hash('sha256', json_encode([
            'type' => $key->type(),
            'value' => $key->nodeValue(),
        ], JSON_THROW_ON_ERROR));
    }
}
