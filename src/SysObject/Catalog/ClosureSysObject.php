<?php

declare(strict_types=1);

namespace Elfennol\MonkeyPhp\SysObject\Catalog;

use Elfennol\MonkeyPhp\SysObject\SysObjectInterface;
use Elfennol\MonkeyPhp\SysObject\SysObjectType;
use Stringable;

readonly class ClosureSysObject implements SysObjectInterface, Stringable
{
    /**
     * @param SysObjectInterface[] $freeVars
     */
    public function __construct(
        private CompiledFnSysObject $compiledFn,
        private array $freeVars,
    ) {
    }

    public function compiledFn(): CompiledFnSysObject
    {
        return $this->compiledFn;
    }

    /**
     * @return SysObjectInterface[]
     */
    public function freeVars(): array
    {
        return $this->freeVars;
    }

    public function type(): SysObjectType
    {
        return SysObjectType::Closure;
    }

    public function __toString(): string
    {
        return 'closure';
    }
}
