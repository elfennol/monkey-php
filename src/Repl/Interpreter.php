<?php

declare(strict_types=1);

namespace Elfennol\MonkeyPhp\Repl;

use Elfennol\MonkeyPhp\Evaluator\EvaluatorInterface;
use Elfennol\MonkeyPhp\Evaluator\Macro\MacroExpansion;
use Elfennol\MonkeyPhp\Lexer\LexerBuilderInterface;
use Elfennol\MonkeyPhp\Parser\ParserInterface;
use Elfennol\MonkeyPhp\SysObject\Context\ContextInterface;
use Elfennol\MonkeyPhp\SysObject\SysObjectInterface;
use Elfennol\MonkeyPhp\Utils\String\StringBuilder;
use Elfennol\MonkeyPhp\Utils\String\StringUtils;

readonly class Interpreter
{
    public function __construct(
        private LexerBuilderInterface $lexerBuilder,
        private ParserInterface $parser,
        private EvaluatorInterface $evaluator,
        private ContextInterface $context,
        private MacroExpansion $macroExpansion,
    ) {
    }

    public function read(string $input): SysObjectInterface
    {
        $lexer = $this->lexerBuilder->build((new StringBuilder(new StringUtils()))->build($input));
        $ast = $this->parser->parse($lexer);

        $newAst = $this->macroExpansion->defineMacros($ast, $this->context);
        $expanded = $this->macroExpansion->expandMacros($newAst, $this->context);

        return $this->evaluator->evaluate($expanded, $this->context);
    }
}
