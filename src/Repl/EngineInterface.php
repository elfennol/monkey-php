<?php

declare(strict_types=1);

namespace Elfennol\MonkeyPhp\Repl;

use Elfennol\MonkeyPhp\Compiler\Symbol\SymbolTable;
use Elfennol\MonkeyPhp\SysObject\ConstantTypeInterface;
use Elfennol\MonkeyPhp\Utils\Option\None;
use Elfennol\MonkeyPhp\Utils\Option\Option;
use Elfennol\MonkeyPhp\Vm\Globals;

interface EngineInterface
{
    /**
     * @param Option<SymbolTable> $symbolTable
     * @param Option<array<int, ConstantTypeInterface>> $constant
     * @param Option<Globals> $globals
     */
    public function read(
        string $input,
        Option $symbolTable = new None(),
        Option $constant = new None(),
        Option $globals = new None()
    ): EngineResult;
}
