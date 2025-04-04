<?php

declare(strict_types=1);

namespace Elfennol\MonkeyPhp\Repl;

use Elfennol\MonkeyPhp\Compiler\CompilerInterface;
use Elfennol\MonkeyPhp\Evaluator\Macro\MacroExpansion;
use Elfennol\MonkeyPhp\Lexer\LexerBuilderInterface;
use Elfennol\MonkeyPhp\Parser\ParserInterface;
use Elfennol\MonkeyPhp\SysObject\Context\ContextInterface;
use Elfennol\MonkeyPhp\Utils\Option\None;
use Elfennol\MonkeyPhp\Utils\Option\Option;
use Elfennol\MonkeyPhp\Utils\Option\Some;
use Elfennol\MonkeyPhp\Utils\String\StringBuilder;
use Elfennol\MonkeyPhp\Utils\String\StringUtils;
use Elfennol\MonkeyPhp\Vm\VmInterface;

readonly class CompilerRepl implements EngineInterface
{
    public function __construct(
        private LexerBuilderInterface $lexerBuilder,
        private ParserInterface $parser,
        private CompilerInterface $compiler,
        private ContextInterface $context,
        private MacroExpansion $macroExpansion,
        private VmInterface $vm,
    ) {
    }

    public function read(
        string $input,
        Option $symbolTable = new None(),
        Option $constant = new None(),
        Option $globals = new None(),
    ): EngineResult {
        $lexer = $this->lexerBuilder->build(new StringBuilder(new StringUtils())->build($input));
        $ast = $this->parser->parse($lexer);

        $newAst = $this->macroExpansion->defineMacros($ast, $this->context);
        $expanded = $this->macroExpansion->expandMacros($newAst, $this->context);
        $compilerResult = $this->compiler->compile($expanded, $symbolTable, $constant);
        $vmResult = $this->vm->run($compilerResult->byteCode, $globals);

        return new EngineResult(
            $vmResult->sysObject,
            new Some($compilerResult->symbolTable),
            new Some($compilerResult->constants),
            new Some($vmResult->globals),
        );
    }
}
