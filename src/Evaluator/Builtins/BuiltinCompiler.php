<?php

declare(strict_types=1);

namespace Elfennol\MonkeyPhp\Evaluator\Builtins;

use Closure;
use Elfennol\MonkeyPhp\Evaluator\Builtins\Catalog\EchoBuiltin;
use Elfennol\MonkeyPhp\Evaluator\Builtins\Catalog\FirstBuiltin;
use Elfennol\MonkeyPhp\Evaluator\Builtins\Catalog\LastBuiltin;
use Elfennol\MonkeyPhp\Evaluator\Builtins\Catalog\LenBuiltin;
use Elfennol\MonkeyPhp\Evaluator\Builtins\Catalog\PushBuiltin;
use Elfennol\MonkeyPhp\Evaluator\Builtins\Catalog\RestBuiltin;
use Elfennol\MonkeyPhp\SysObject\Catalog\BuiltinSysObject;
use Elfennol\MonkeyPhp\SysObject\Context\Builtins;
use Elfennol\MonkeyPhp\SysObject\Context\BuiltinsInterface;

readonly class BuiltinCompiler
{
    public function __construct(
        private EchoBuiltin $echoBuiltin,
        private FirstBuiltin $firstBuiltin,
        private LastBuiltin $lastBuiltin,
        private LenBuiltin $lenBuiltin,
        private PushBuiltin $pushBuiltin,
        private RestBuiltin $restBuiltin,
    ) {
    }

    public function compile(): BuiltinsInterface
    {
        $builtins = [
            BuiltinName::Echo->value => ($this->echoBuiltin)->exec(...),
            BuiltinName::First->value => ($this->firstBuiltin)->exec(...),
            BuiltinName::Last->value => ($this->lastBuiltin)->exec(...),
            BuiltinName::Len->value => ($this->lenBuiltin)->exec(...),
            BuiltinName::Push->value => ($this->pushBuiltin)->exec(...),
            BuiltinName::Rest->value => ($this->restBuiltin)->exec(...),
        ];

        return new Builtins(
            array_map(static fn (Closure $builtinClosure) => new BuiltinSysObject($builtinClosure), $builtins)
        );
    }
}
