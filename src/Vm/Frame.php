<?php

declare(strict_types=1);

namespace Elfennol\MonkeyPhp\Vm;

use Elfennol\MonkeyPhp\ByteCode\ByteCodeReader;
use Elfennol\MonkeyPhp\SysObject\Catalog\ClosureSysObject;
use Elfennol\MonkeyPhp\SysObject\SysObjectInterface;
use Elfennol\MonkeyPhp\Utils\Option\None;
use Elfennol\MonkeyPhp\Utils\Option\Option;

class Frame
{
    /** @var SysObjectInterface[] */
    public array $locals = [];

    /**
     * @param Option<ClosureSysObject> $closure
     */
    public function __construct(
        public readonly ByteCodeReader $byteCodeReader,
        public readonly Option $closure = new None(),
    ) {
    }
}
