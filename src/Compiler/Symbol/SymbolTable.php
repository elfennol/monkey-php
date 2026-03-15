<?php

declare(strict_types=1);

namespace Elfennol\MonkeyPhp\Compiler\Symbol;

use Elfennol\MonkeyPhp\Compiler\CompilerException;
use Elfennol\MonkeyPhp\Compiler\CompilerExceptionType;
use Elfennol\MonkeyPhp\Utils\Option\None;
use Elfennol\MonkeyPhp\Utils\Option\Option;

class SymbolTable
{
    /** @var array<string, Symbol> */
    private array $store;

    private int $numDefinitions;

    private int $numBuiltins;

    /** @var Symbol[] */
    private array $freeSymbols;

    /**
     * @param Option<SymbolTable> $outer
     */
    public function __construct(private readonly Option $outer = new None())
    {
        $this->store = [];
        $this->numDefinitions = 0;
        $this->numBuiltins = 0;
        $this->freeSymbols = [];
    }

    public function defineBuiltin(string $name): Symbol
    {
        $this->store[$name] = new Symbol($name, Scope::Builtin, $this->numBuiltins++);

        return $this->store[$name];
    }

    public function define(string $name): Symbol
    {
        $scope = $this->outer->isSome() ? Scope::Local : Scope::Global;
        $this->store[$name] = new Symbol($name, $scope, $this->numDefinitions++);

        return $this->store[$name];
    }

    public function resolve(string $name): Symbol
    {
        if (!isset($this->store[$name])) {
            if ($this->outer->isSome()) {
                $symbol = $this->outer->unwrap()->resolve($name);

                if ($symbol->scope === Scope::Global || $symbol->scope === Scope::Builtin) {
                    return $symbol;
                }

                return $this->defineFree($symbol);
            }

            throw new CompilerException(
                CompilerExceptionType::SymbolNotFound,
                ['name' => $name],
                'Symbol not found in symbol table.'
            );
        }

        return $this->store[$name];
    }

    public function defineFunction(string $name): Symbol
    {
        $this->store[$name] = new Symbol($name, Scope::Function, 0);

        return $this->store[$name];
    }

    private function defineFree(Symbol $original): Symbol
    {
        $this->freeSymbols[] = $original;
        $index = count($this->freeSymbols) - 1;
        $this->store[$original->name] = new Symbol($original->name, Scope::Free, $index);

        return $this->store[$original->name];
    }

    /**
     * @return Symbol[]
     */
    public function freeSymbols(): array
    {
        return $this->freeSymbols;
    }
}
