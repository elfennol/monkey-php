<?php

declare(strict_types=1);

namespace Elfennol\MonkeyPhp\SysObject;

interface AtomSysObjectInterface extends SysObjectInterface
{
    public function nodeValue(): string;

    public function nativeValue(): bool|int|string;
}
