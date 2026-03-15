<?php

declare(strict_types=1);

namespace Elfennol\MonkeyPhp\Vm;

use Elfennol\MonkeyPhp\ByteCode\ByteCode;
use Elfennol\MonkeyPhp\Utils\Option\None;
use Elfennol\MonkeyPhp\Utils\Option\Option;

interface VmInterface
{
    /**
     * @param Option<Globals> $globals
     */
    public function run(ByteCode $byteCode, Option $globals = new None()): VmResult;
}
