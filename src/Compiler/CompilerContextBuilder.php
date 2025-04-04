<?php

declare(strict_types=1);

namespace Elfennol\MonkeyPhp\Compiler;

use Closure;
use Elfennol\MonkeyPhp\ByteCode\ByteCodeWriterBuilder;
use Elfennol\MonkeyPhp\Compiler\Symbol\SymbolTable;
use Elfennol\MonkeyPhp\SysObject\Builtins\BuiltinName;
use Elfennol\MonkeyPhp\SysObject\ConstantTypeInterface;
use Elfennol\MonkeyPhp\Utils\Option\None;
use Elfennol\MonkeyPhp\Utils\Option\Option;

readonly class CompilerContextBuilder
{
    public function __construct(private ByteCodeWriterBuilder $byteCodeWriterBuilder)
    {
    }

    /**
     * @param Option<SymbolTable> $symbolTable
     * @param Option<array<int, ConstantTypeInterface>> $constant
     */
    public function build(
        Closure $processor,
        Option $symbolTable = new None(),
        Option $constant = new None()
    ): CompilerContext {
        $table = $symbolTable->isNone() ? new SymbolTable() : $symbolTable->unwrap();

        if ($symbolTable->isNone()) {
            foreach (BuiltinName::cases() as $builtin) {
                $table->defineBuiltin($builtin->value);
            }
        }

        return new CompilerContext(
            $processor,
            $this->byteCodeWriterBuilder->build($constant),
            $table
        );
    }
}
