<?php

declare(strict_types=1);

namespace Elfennol\MonkeyPhp\Compiler;

use Elfennol\MonkeyPhp\Compiler\Symbol\SymbolTable;
use Elfennol\MonkeyPhp\Node\NodeInterface;
use Elfennol\MonkeyPhp\SysObject\ConstantTypeInterface;
use Elfennol\MonkeyPhp\Utils\Option\None;
use Elfennol\MonkeyPhp\Utils\Option\Option;

interface CompilerInterface
{
    /**
     * @param Option<SymbolTable> $symbolTable
     * @param Option<array<int, ConstantTypeInterface>> $constant
     */
    public function compile(
        NodeInterface $node,
        Option $symbolTable = new None(),
        Option $constant = new None()
    ): CompilerResult;
}
