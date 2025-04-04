<?php

declare(strict_types=1);

namespace Elfennol\MonkeyPhp\Tests;

use Elfennol\MonkeyPhp\ContainerBuilder;
use Elfennol\MonkeyPhp\Vm\Vm;

trait VmFactoryTrait
{
    public function createVm(): Vm
    {
        return new ContainerBuilder()->build()->get(Vm::class);
    }
}
