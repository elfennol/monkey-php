<?php

declare(strict_types=1);

namespace Elfennol\MonkeyPhp\Tests\Parser;

use Elfennol\MonkeyPhp\Parser\ParserException;
use Elfennol\MonkeyPhp\Parser\ParserExceptionType;
use Elfennol\MonkeyPhp\Tests\LexerFactoryTrait;
use Elfennol\MonkeyPhp\Tests\LoadFixturesTrait;
use Elfennol\MonkeyPhp\Tests\ParserFactoryTrait;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class ParserTest extends TestCase
{
    use LexerFactoryTrait;
    use ParserFactoryTrait;
    use LoadFixturesTrait;

    /**
     * We just test the statements here.
     */
    public function testParse(): void
    {
        $input = <<<'MONKEY'
let foo = 42;
foo = 40;
return 123;
return;
MONKEY;

        $expected = <<<'JSON'
{
    ":Program": [
        {
            ":Let": [
                {
                    ":foo": []
                },
                {
                    ":42": []
                }
            ]
        },
        {
            ":Assign": [
                {
                    ":foo": []
                },
                {
                    ":40": []
                }
            ]
        },
        {
            ":Return": [
                {
                    ":123": []
                }
            ]
        },
        {
            ":Return": []
        }
    ]
}
JSON;


        $parser = $this->createParser();
        $ast = $parser->parse($this->createLexer($input));
        $astEncoded = json_encode($ast, JSON_PRETTY_PRINT);

        self::assertJsonStringEqualsJsonString($expected, (string)$astEncoded);
    }

    #[DataProvider('parserErrorProvider')]
    public function testParseWithError(
        string $input,
        ParserExceptionType $exceptionType,
        string $exceptionMsg,
    ): void {
        $parser = $this->createParser();

        try {
            $parser->parse($this->createLexer($input));
            self::fail('Expected ParserException');
        } catch (ParserException $parserException) {
            self::assertNotEmpty($exceptionMsg);
            self::assertStringStartsWith($exceptionMsg, $parserException->getMessage());
            self::assertSame($exceptionType, $parserException->getType());
            self::assertArrayHasKey('currentToken', $parserException->getContext());
        }
    }

    /**
     * @return array<int, array<string|ParserExceptionType>>
     */
    public static function parserErrorProvider(): array
    {
        return [
            ['@', ParserExceptionType::TokenMustBePrefix, 'Unable to parse prefix expression.'],
            ['1()', ParserExceptionType::NodeTypeInvalid, 'Unable to parse infix FnCall expression.'],
            ['fn=', ParserExceptionType::TokenTypeInvalid, 'The eater has indigestion.'],
        ];
    }
}
