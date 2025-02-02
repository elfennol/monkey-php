<?php

declare(strict_types=1);

namespace Elfennol\MonkeyPhp\SysObject\Catalog;

use Closure;
use Elfennol\MonkeyPhp\Node\NodeInterface;
use Elfennol\MonkeyPhp\SysObject\Context\ContextInterface;
use Elfennol\MonkeyPhp\SysObject\SysObjectInterface;
use Elfennol\MonkeyPhp\SysObject\SysObjectType;

readonly class MacroBuiltinSysObject implements SysObjectInterface
{
    /**
     * @param Closure(ContextInterface, NodeInterface...): SysObjectInterface $macroBuiltinFn
     */
    public function __construct(private Closure $macroBuiltinFn)
    {
    }

    public function type(): SysObjectType
    {
        return SysObjectType::MacroBuiltin;
    }

    /**
     * @return Closure(ContextInterface, NodeInterface...): SysObjectInterface
     */
    public function macroBuiltinFn(): Closure
    {
        return $this->macroBuiltinFn;
    }
}
