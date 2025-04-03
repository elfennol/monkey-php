<?php

declare(strict_types=1);

namespace Elfennol\MonkeyPhp\Tests;

use Elfennol\MonkeyPhp\Evaluator\EvaluatorException;
use Elfennol\MonkeyPhp\Evaluator\EvaluatorExceptionType;
use Elfennol\MonkeyPhp\Evaluator\EvaluatorInterface;
use Elfennol\MonkeyPhp\Lexer\LexerInterface;
use Elfennol\MonkeyPhp\Parser\ParserInterface;
use Elfennol\MonkeyPhp\SysObject\AtomSysObjectInterface;
use Elfennol\MonkeyPhp\SysObject\Context\ContextInterface;
use Stringable;

/**
 * @internal
 */
trait EvaluatorAssertionTrait
{
    abstract private static function fail(string $message = ''): never;

    abstract private static function assertInstanceOf(string $expected, mixed $actual, string $message = ''): void;

    abstract private static function assertSame(mixed $expected, mixed $actual, string $message = ''): void;

    abstract private function createParser(): ParserInterface;

    abstract private function createLexer(string $input): LexerInterface;

    abstract private function createEvaluator(): EvaluatorInterface;

    abstract private function createContext(): ContextInterface;

    private function assertEvaluator(string $input, ?string $expectedValue, string $expectedObject): void
    {
        if (!class_exists($expectedObject)) {
            self::fail(sprintf('%s does not exist.', $expectedObject));
        }

        $parser = $this->createParser();
        $nodes = $parser->parse($this->createLexer($input));
        $sysObject = $this->createEvaluator()->evaluate($nodes, $this->createContext());

        self::assertInstanceOf($expectedObject, $sysObject);

        if ($sysObject instanceof AtomSysObjectInterface) {
            self::assertSame($expectedValue, $sysObject->nodeValue());
        }

        if ($sysObject instanceof Stringable) {
            self::assertSame($expectedValue, $sysObject->__toString());
        }
    }

    private function assertEvaluatorException(
        string $input,
        EvaluatorExceptionType $exceptionType,
        string $exceptionMsg
    ): void {
        $parser = $this->createParser();
        $nodes = $parser->parse($this->createLexer($input));

        try {
            $this->createEvaluator()->evaluate($nodes, $this->createContext());
            self::fail('Expected EvaluatorException');
        } catch (EvaluatorException $evaluatorException) {
            self::assertSame($exceptionMsg, $evaluatorException->getMessage());
            self::assertSame($exceptionType, $evaluatorException->getType());
            self::assertArrayHasKey('node', $evaluatorException->getContext());
        }
    }
}
