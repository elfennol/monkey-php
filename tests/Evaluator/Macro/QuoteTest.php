<?php

declare(strict_types=1);

namespace Elfennol\MonkeyPhp\Tests\Evaluator\Macro;

use Elfennol\MonkeyPhp\Evaluator\EvaluatorExceptionType;
use Elfennol\MonkeyPhp\SysObject\Catalog\QuoteSysObject;
use Elfennol\MonkeyPhp\Tests\EvaluatorAssertionTrait;
use Elfennol\MonkeyPhp\Tests\EvaluatorFactoryTrait;
use Elfennol\MonkeyPhp\Tests\LexerFactoryTrait;
use Elfennol\MonkeyPhp\Tests\ParserFactoryTrait;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class QuoteTest extends TestCase
{
    use LexerFactoryTrait;
    use EvaluatorAssertionTrait;
    use EvaluatorFactoryTrait;
    use ParserFactoryTrait;

    #[DataProvider('quoteProvider')]
    public function testQuote(string $input, ?string $expectedValue, string $expectedObject): void
    {
        $this->assertEvaluator($input, $expectedValue, $expectedObject);
    }

    #[DataProvider('quoteUnquoteProvider')]
    public function testQuoteUnquote(string $input, ?string $expectedValue, string $expectedObject): void
    {
        $this->assertEvaluator($input, $expectedValue, $expectedObject);
    }

    #[DataProvider('quoteErrorProvider')]
    public function testQuoteWithError(
        string $input,
        EvaluatorExceptionType $exceptionType,
        string $exceptionMsg
    ): void {
        $this->assertEvaluatorException($input, $exceptionType, $exceptionMsg);
    }

    /**
     * @return array<int, array{string, string, string}>
     */
    public static function quoteProvider(): array
    {
        $expectedAdd = <<<'EXPECTED'
{
    ":+": [
        {
            ":2": []
        },
        {
            ":3": []
        }
    ]
}
EXPECTED;

        $expectedIdentifier = <<<'EXPECTED'
{
    ":foo": []
}
EXPECTED;


        return [
            ['quote(2 + 3);', $expectedAdd, QuoteSysObject::class],
            ['quote(foo);', $expectedIdentifier, QuoteSysObject::class],
        ];
    }

    /**
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     *
     * @return array<int, array{string, string, string}>
     */
    public static function quoteUnquoteProvider(): array
    {
        $expectedAtom = <<<'EXPECTED'
{
    ":4": []
}
EXPECTED;
        $expectedAdd = <<<'EXPECTED'
{
    ":6": []
}
EXPECTED;
        $expectedQuoteUnquote = <<<'EXPECTED'
{
    ":+": [
        {
            ":8": []
        },
        {
            ":6": []
        }
    ]
}
EXPECTED;
        $expectedUnquoteQuote = <<<'EXPECTED'
{
    ":+": [
        {
            ":6": []
        },
        {
            ":8": []
        }
    ]
}
EXPECTED;
        $expectedQuoteIndent = <<<'EXPECTED'
{
    ":foo": []
}
EXPECTED;
        $expectedUnquoteInd = <<<'EXPECTED'
{
    ":2": []
}
EXPECTED;
        $expectedUnquoteTrue = <<<'EXPECTED'
{
    ":true": []
}
EXPECTED;
        $expectedUnquoteFalse = <<<'EXPECTED'
{
    ":false": []
}
EXPECTED;
        $expectedInside = <<<'EXPECTED'
{
    ":+": [
        {
            ":4": []
        },
        {
            ":4": []
        }
    ]
}
EXPECTED;
        $expectedInsideLet = <<<'EXPECTED'
{
    ":+": [
        {
            ":8": []
        },
        {
            ":+": [
                {
                    ":4": []
                },
                {
                    ":4": []
                }
            ]
        }
    ]
}
EXPECTED;

        return [
            ['quote(unquote(4));', $expectedAtom, QuoteSysObject::class],
            ['quote(unquote(4 + 2));', $expectedAdd, QuoteSysObject::class],
            ['quote(8 + unquote(4 + 2));', $expectedQuoteUnquote, QuoteSysObject::class],
            ['quote(unquote(4 + 2) + 8);', $expectedUnquoteQuote, QuoteSysObject::class],
            ['let foo = 2; quote(foo);', $expectedQuoteIndent, QuoteSysObject::class],
            ['let foo = 2; quote(unquote(foo));', $expectedUnquoteInd, QuoteSysObject::class],
            ['quote(unquote(true));', $expectedUnquoteTrue, QuoteSysObject::class],
            ['quote(unquote(true == false));', $expectedUnquoteFalse, QuoteSysObject::class],
            ['quote(unquote(quote(4 + 4)));', $expectedInside, QuoteSysObject::class],
            [
                'let quotedInfixExpression = quote(4 + 4); quote(unquote(4 + 4) + unquote(quotedInfixExpression));',
                $expectedInsideLet,
                QuoteSysObject::class
            ],
        ];
    }

    /**
     * @return array{array{string, EvaluatorExceptionType, string}}
     */
    public static function quoteErrorProvider(): array
    {
        return [
            ['quote();', EvaluatorExceptionType::FnWrongArgsNumber, 'Unable to evaluate quote.'],
            ['quote(2, 3);', EvaluatorExceptionType::FnWrongArgsNumber, 'Unable to evaluate quote.'],
        ];
    }
}
