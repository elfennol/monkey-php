<?php

declare(strict_types=1);

namespace Elfennol\MonkeyPhp\SysObject\Context;

use Elfennol\MonkeyPhp\Utils\Option\Option;

interface ContextInterface
{
    public function env(): EnvInterface;

    public function builtins(): BuiltinsInterface;

    public function macroBuiltins(): MacroBuiltinsInterface;

    public function exists(string $name): bool;

    /**
     * @param Option<EnvInterface> $outer
     */
    public function buildWithEnv(Option $outer): ContextInterface;
}
