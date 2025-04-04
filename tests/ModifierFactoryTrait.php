<?php

declare(strict_types=1);

namespace Elfennol\MonkeyPhp\Tests;

use Elfennol\MonkeyPhp\ContainerBuilder;
use Elfennol\MonkeyPhp\Evaluator\ModifierInterface;

/**
 * @internal
 */
trait ModifierFactoryTrait
{
    private function createModifier(): ModifierInterface
    {
        return new ContainerBuilder()->build()->get(ModifierInterface::class);
    }
}
