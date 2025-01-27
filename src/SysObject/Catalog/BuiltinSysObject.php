<?php

declare(strict_types=1);

namespace Elfennol\MonkeyPhp\SysObject\Catalog;

use Closure;
use Elfennol\MonkeyPhp\SysObject\SysObjectInterface;
use Elfennol\MonkeyPhp\SysObject\SysObjectType;

readonly class BuiltinSysObject implements SysObjectInterface
{
    /**
     * @param Closure(SysObjectInterface...): SysObjectInterface $builtinFn
     */
    public function __construct(private Closure $builtinFn)
    {
    }

    public function type(): SysObjectType
    {
        return SysObjectType::Builtin;
    }

    /**
     * @return Closure(SysObjectInterface...): SysObjectInterface
     */
    public function builtinFn(): Closure
    {
        return $this->builtinFn;
    }
}
