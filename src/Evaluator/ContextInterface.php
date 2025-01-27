<?php

declare(strict_types=1);

namespace Elfennol\MonkeyPhp\Evaluator;

use Elfennol\MonkeyPhp\Evaluator\Builtins\Builtins;
use Elfennol\MonkeyPhp\SysObject\Environment\EnvInterface;
use Elfennol\MonkeyPhp\Utils\Option\Option;

interface ContextInterface
{
    public function env(): EnvInterface;

    public function builtins(): Builtins;

    /**
     * @param Option<EnvInterface> $outer
     */
    public function fromEnv(Option $outer): Context;
}
