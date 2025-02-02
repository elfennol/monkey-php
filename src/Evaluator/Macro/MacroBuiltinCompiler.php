<?php

declare(strict_types=1);

namespace Elfennol\MonkeyPhp\Evaluator\Macro;

use Closure;
use Elfennol\MonkeyPhp\Evaluator\Macro\Catalog\QuoteMacroBuiltin;
use Elfennol\MonkeyPhp\SysObject\Catalog\MacroBuiltinSysObject;
use Elfennol\MonkeyPhp\SysObject\Context\MacroBuiltins;
use Elfennol\MonkeyPhp\SysObject\Context\MacroBuiltinsInterface;

readonly class MacroBuiltinCompiler
{
    public function __construct(
        private QuoteMacroBuiltin $quote
    ) {
    }

    public function compile(): MacroBuiltinsInterface
    {
        $builtins = [
            MacroBuiltinName::Quote->value => ($this->quote)->exec(...),
        ];

        return new MacroBuiltins(
            array_map(static fn (Closure $builtinClosure) => new MacroBuiltinSysObject($builtinClosure), $builtins)
        );
    }
}
