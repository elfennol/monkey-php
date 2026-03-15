<?php

declare(strict_types=1);

namespace Elfennol\MonkeyPhp\Repl;

use Elfennol\MonkeyPhp\Compiler\Symbol\SymbolTable;
use Elfennol\MonkeyPhp\SysObject\ConstantTypeInterface;
use Elfennol\MonkeyPhp\SysObject\SysObjectInterface;
use Elfennol\MonkeyPhp\Utils\Option\None;
use Elfennol\MonkeyPhp\Utils\Option\Option;
use Elfennol\MonkeyPhp\Vm\Globals;

readonly class EngineResult
{
    /**
     * @param Option<SymbolTable> $symbolTable
     * @param Option<array<int, ConstantTypeInterface>> $constants
     * @param Option<Globals> $globals
     */
    public function __construct(
        public SysObjectInterface $sysObject,
        public Option $symbolTable = new None(),
        public Option $constants = new None(),
        public Option $globals = new None(),
    ) {
    }
}
