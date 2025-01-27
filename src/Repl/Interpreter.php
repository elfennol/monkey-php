<?php

declare(strict_types=1);

namespace Elfennol\MonkeyPhp\Repl;

use Elfennol\MonkeyPhp\Evaluator\ContextInterface;
use Elfennol\MonkeyPhp\Evaluator\EvaluatorInterface;
use Elfennol\MonkeyPhp\Lexer\LexerBuilderInterface;
use Elfennol\MonkeyPhp\Parser\ParserInterface;
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
    ) {
    }

    public function read(string $input): SysObjectInterface
    {
        $lexer = $this->lexerBuilder->build((new StringBuilder(new StringUtils()))->build($input));

        return $this->evaluator->evaluate($this->parser->parse($lexer), $this->context);
    }
}
