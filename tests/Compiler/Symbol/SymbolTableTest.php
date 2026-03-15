<?php

declare(strict_types=1);

namespace Elfennol\MonkeyPhp\Tests\Compiler\Symbol;

use Elfennol\MonkeyPhp\Compiler\CompilerException;
use Elfennol\MonkeyPhp\Compiler\Symbol\Scope;
use Elfennol\MonkeyPhp\Compiler\Symbol\SymbolTable;
use PHPUnit\Framework\TestCase;

class SymbolTableTest extends TestCase
{
    private SymbolTable $symbolTable;

    protected function setUp(): void
    {
        $this->symbolTable = new SymbolTable();
    }

    public function testDefineReturnsSymbolWithCorrectNameAndScope(): void
    {
        $symbol = $this->symbolTable->define('a');

        self::assertSame('a', $symbol->name);
        self::assertSame(Scope::Global, $symbol->scope);
    }

    public function testDefineReturnsSymbolWithIncrementedIndex(): void
    {
        $symbolA = $this->symbolTable->define('a');
        $symbolB = $this->symbolTable->define('b');
        $symbolC = $this->symbolTable->define('c');

        self::assertSame(0, $symbolA->index);
        self::assertSame(1, $symbolB->index);
        self::assertSame(2, $symbolC->index);
    }

    public function testResolveReturnsPreviouslyDefinedSymbol(): void
    {
        $defined = $this->symbolTable->define('a');
        $resolved = $this->symbolTable->resolve('a');

        self::assertSame($defined, $resolved);
    }

    public function testResolveThrowsExceptionWhenSymbolNotFound(): void
    {
        $this->expectException(CompilerException::class);

        $this->symbolTable->resolve('unknown');
    }

    public function testDefineOverwritesExistingSymbol(): void
    {
        $this->symbolTable->define('a');
        $redefined = $this->symbolTable->define('a');

        self::assertSame(1, $redefined->index);
    }
}
