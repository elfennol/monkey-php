<?php

declare(strict_types=1);

namespace Elfennol\MonkeyPhp\Tests\Parser\PrattParser;

use Elfennol\MonkeyPhp\Parser\PrattParser\BindingPowerSet;
use Elfennol\MonkeyPhp\Tests\LexerFactoryTrait;
use Elfennol\MonkeyPhp\Tests\LoadFixturesTrait;
use Elfennol\MonkeyPhp\Tests\ParserFactoryTrait;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class PrattParserTest extends TestCase
{
    use LexerFactoryTrait;
    use ParserFactoryTrait;
    use LoadFixturesTrait;

    /**
     * Test the associativity of expressions in PrattParserTest.
     */
    #[DataProvider('parseProvider')]
    public function testParse(string $input, string $expectedFixtureName): void
    {
        $this->assertPrattParser($input, $expectedFixtureName);
    }

    /**
     * @return string[][]
     */
    public static function parseProvider(): array
    {
        return [
            ['1 + -12 * 3 * 16 / 8 - 4**2 + 5**6**7 + 20! * (50 + 60) + 42', 'pratt_parser_result.json'],
            ['foo + true + !false + 10 > 5 + 6 + 1 < 2 + 3 == 4 != 3', 'pratt_parser_ident_bool_result.json'],
            ['6 * (0 + (2 + 3 * (4 + 5))) + 42', 'pratt_parser_grouped_result.json'],
            ['1 + if (x > 10) { 10 } else { 11 } + 42', 'pratt_parser_if_result.json'],
            [
                'fn() {} + fn(x) { return 42; } + fn (x, y) { let z = 1; return x + y + z; }',
                'pratt_parser_fn_result.json'
            ],
            ['1 + add(x, y)', 'pratt_parser_fn_call_result.json'],
            ['1 + fn(x, y) { return x + y; }(x, y)', 'pratt_parser_fn_call_literal_result.json'],
            ['"Hello World!"', 'pratt_parser_string_result.json'],
            ['[] + [1] + [1 + 2, 3]', 'pratt_parser_array_result.json'],
            ['[1 + 2, 3][1] + foo[1] + foo()[1]', 'pratt_parser_index_result.json'],
            ['{} + { "foo": "bar" } + { "foo": "bar", 1: "int", true: "bool" }', 'pratt_parser_hashmap_result.json'],
            ['macro(x, y) { x + y; }', 'pratt_parser_macro_result.json'],
        ];
    }

    private function assertPrattParser(string $input, string $expectedFixtureName): void
    {
        $prattParser = $this->createPrattParser();
        $exprNode = $prattParser->parse($this->createLexer($input), BindingPowerSet::MIN);

        // JSON_PRETTY_PRINT is just here to make debugging easier.
        $exprNodeEncoded = json_encode($exprNode, JSON_PRETTY_PRINT);

        self::assertJsonStringEqualsJsonString(
            $this->loadFixture($expectedFixtureName),
            (string)$exprNodeEncoded
        );
    }
}
