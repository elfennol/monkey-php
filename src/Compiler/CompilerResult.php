<?php

declare(strict_types=1);

namespace Elfennol\MonkeyPhp\Compiler;

use Elfennol\MonkeyPhp\ByteCode\ByteCode;
use Elfennol\MonkeyPhp\Compiler\Symbol\SymbolTable;
use Elfennol\MonkeyPhp\SysObject\ConstantTypeInterface;

readonly class CompilerResult
{
    /**
     * @param SymbolTable $symbolTable
     * @param array<int, ConstantTypeInterface> $constants
     */
    public function __construct(public ByteCode $byteCode, public SymbolTable $symbolTable, public array $constants)
    {
    }
}
