<?php

namespace Elfennol\MonkeyPhp\Tests;

use Elfennol\MonkeyPhp\ContainerBuilder;
use Elfennol\MonkeyPhp\Parser\ExprParserInterface;
use Elfennol\MonkeyPhp\Parser\ParserInterface;

/**
 * @internal
 */
trait ParserFactoryTrait
{
    private function createParser(): ParserInterface
    {
        return new ContainerBuilder()->build()->get(ParserInterface::class);
    }

    private function createPrattParser(): ExprParserInterface
    {
        return new ContainerBuilder()->build()->get(ExprParserInterface::class);
    }
}
