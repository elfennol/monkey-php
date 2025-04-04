<?php

declare(strict_types=1);

namespace Elfennol\MonkeyPhp\Tests;

use Elfennol\MonkeyPhp\Compiler\AssemblerProducer;
use Elfennol\MonkeyPhp\Compiler\Compiler;
use Elfennol\MonkeyPhp\ContainerBuilder;

trait CompilerFactoryTrait
{
    public function createCompiler(): Compiler
    {
        return new ContainerBuilder()->build()->get(Compiler::class);
    }

    public function createAssembler(): AssemblerProducer
    {
        return new ContainerBuilder()->build()->get(AssemblerProducer::class);
    }
}
