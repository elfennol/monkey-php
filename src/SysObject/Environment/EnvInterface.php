<?php

declare(strict_types=1);

namespace Elfennol\MonkeyPhp\SysObject\Environment;

use Elfennol\MonkeyPhp\SysObject\SysObjectInterface;
use Elfennol\MonkeyPhp\Utils\Option\Option;

interface EnvInterface
{
    /**
     * @return Option<SysObjectInterface>
     */
    public function get(string $name): Option;

    public function set(string $name, SysObjectInterface $sysObject): void;
}
