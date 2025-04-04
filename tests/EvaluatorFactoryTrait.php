<?php

declare(strict_types=1);

namespace Elfennol\MonkeyPhp\Tests;

use Elfennol\MonkeyPhp\ContainerBuilder;
use Elfennol\MonkeyPhp\Evaluator\EvaluatorInterface;
use Elfennol\MonkeyPhp\SysObject\Context\ContextInterface;

/**
 * @internal
 */
trait EvaluatorFactoryTrait
{
    private function createEvaluator(): EvaluatorInterface
    {
        return new ContainerBuilder()->build()->get(EvaluatorInterface::class);
    }

    private function createContext(): ContextInterface
    {
        return new ContainerBuilder()->build()->get(ContextInterface::class);
    }
}
