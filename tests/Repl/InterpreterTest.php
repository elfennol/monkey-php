<?php

declare(strict_types=1);

namespace Elfennol\MonkeyPhp\Tests\Repl;

use Elfennol\MonkeyPhp\Evaluator\ContextInterface;
use Elfennol\MonkeyPhp\Evaluator\EvaluatorInterface;
use Elfennol\MonkeyPhp\Lexer\LexerBuilderInterface;
use Elfennol\MonkeyPhp\Parser\ParserInterface;
use Elfennol\MonkeyPhp\Repl\Interpreter;
use Elfennol\MonkeyPhp\SysObject\Catalog\UnitSysObject;
use Elfennol\MonkeyPhp\Tests\EvaluatorFactoryTrait;
use Elfennol\MonkeyPhp\Tests\LexerFactoryTrait;
use Elfennol\MonkeyPhp\Tests\ParserFactoryTrait;
use PHPUnit\Framework\TestCase;

class InterpreterTest extends TestCase
{
    use EvaluatorFactoryTrait;
    use LexerFactoryTrait;
    use ParserFactoryTrait;

    private Interpreter $interpreter;
    private LexerBuilderInterface $lexerBuilder;
    private ParserInterface $parser;
    private EvaluatorInterface $evaluator;
    private ContextInterface $context;

    protected function setUp(): void
    {
        $this->lexerBuilder = $this->createLexerBuilder();
        $this->parser = $this->createParser();
        $this->evaluator = $this->createEvaluator();
        $this->context = $this->createContext();

        $this->interpreter = new Interpreter(
            $this->lexerBuilder,
            $this->parser,
            $this->evaluator,
            $this->context,
        );
    }

    public function testInterpreter(): void
    {
        self::assertInstanceOf(UnitSysObject::class, $this->interpreter->read(''));
    }
}
