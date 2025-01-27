<?php

declare(strict_types=1);

namespace Elfennol\MonkeyPhp\Evaluator;

use Elfennol\MonkeyPhp\Evaluator\Builtins\Builtins;
use Elfennol\MonkeyPhp\SysObject\Environment\Env;
use Elfennol\MonkeyPhp\SysObject\Environment\EnvInterface;
use Elfennol\MonkeyPhp\Utils\Option\Option;

readonly class Context implements ContextInterface
{
    public function __construct(private EnvInterface $env, private Builtins $builtins)
    {
    }

    public function env(): EnvInterface
    {
        return $this->env;
    }

    public function builtins(): Builtins
    {
        return $this->builtins;
    }

    public function fromEnv(Option $outer): Context
    {
        return new Context(new Env($outer), $this->builtins);
    }
}
