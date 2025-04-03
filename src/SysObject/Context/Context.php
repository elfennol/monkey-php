<?php

declare(strict_types=1);

namespace Elfennol\MonkeyPhp\SysObject\Context;

use Elfennol\MonkeyPhp\Utils\Option\Option;

readonly class Context implements ContextInterface
{
    public function __construct(
        private EnvInterface $env,
        private BuiltinsInterface $builtins,
        private MacroBuiltinsInterface $macroBuiltins
    ) {
    }

    public function env(): EnvInterface
    {
        return $this->env;
    }

    public function builtins(): BuiltinsInterface
    {
        return $this->builtins;
    }

    public function macroBuiltins(): MacroBuiltinsInterface
    {
        return $this->macroBuiltins;
    }

    public function exists(string $name): bool
    {
        return $this->env()->get($name)->isSome()
            || $this->builtins()->get($name)->isSome()
            || $this->macroBuiltins()->get($name)->isSome();
    }

    public function buildWithEnv(Option $outer): Context
    {
        return new Context(new Env($outer), $this->builtins, $this->macroBuiltins);
    }
}
