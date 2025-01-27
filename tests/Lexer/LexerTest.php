<?php

declare(strict_types=1);

namespace Elfennol\MonkeyPhp\Tests\Lexer;

use Elfennol\MonkeyPhp\Lexer\LexerException;
use Elfennol\MonkeyPhp\Lexer\LexerExceptionType;
use Elfennol\MonkeyPhp\Lexer\LexerInterface;
use Elfennol\MonkeyPhp\Tests\LexerFactoryTrait;
use Elfennol\MonkeyPhp\Token\TokenType;
use Elfennol\MonkeyPhp\Utils\Char\Char;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * @SuppressWarnings(PHPMD.TooManyPublicMethods) Mwahahaha!
 */
class LexerTest extends TestCase
{
    use LexerFactoryTrait;

    public function testIllegalToken(): void
    {
        $this->assertLexer($this->createLexer('🐒'), [[TokenType::Illegal, '🐒']], TokenType::Illegal);
    }

    #[DataProvider('stringProvider')]
    public function testString(string $actual, string $expected): void
    {
        $this->assertLexer(
            $this->createLexer($actual),
            [[TokenType::String, $expected], [TokenType::Eof, "\0"]],
            TokenType::Eof
        );
    }

    public function testArray(): void
    {
        $expectedTokenTypes = [
            [TokenType::Lbracket, Char::LeftBracket->value],
            [TokenType::Int, '1'],
            [TokenType::Comma, Char::Comma->value],
            [TokenType::Int, '2'],
            [TokenType::Rbracket, Char::RightBracket->value],
            [TokenType::Semicolon, Char::Semicolon->value],

            [TokenType::Eof, Char::Nul->value]
        ];

        $this->assertLexer(
            $this->createLexer('[1, 2];'),
            $expectedTokenTypes,
            TokenType::Eof
        );
    }

    public function testStmt(): void
    {
        $input = <<<'STMT'
let foo = 12;
foo = 34;
return 56;
STMT;

        $expectedTokenTypes = [
            [TokenType::Let, 'let'],
            [TokenType::Identifier, 'foo'],
            [TokenType::Assign, '='],
            [TokenType::Int, '12'],
            [TokenType::Semicolon, Char::Semicolon->value],

            [TokenType::Identifier, 'foo'],
            [TokenType::Assign, '='],
            [TokenType::Int, '34'],
            [TokenType::Semicolon, Char::Semicolon->value],

            [TokenType::Return, 'return'],
            [TokenType::Int, '56'],
            [TokenType::Semicolon, Char::Semicolon->value],

            [TokenType::Eof, Char::Nul->value],
        ];

        $this->assertLexer(
            $this->createLexer($input),
            $expectedTokenTypes,
            TokenType::Eof
        );
    }

    public function testFn(): void
    {
        $input = <<<'FN'
let add = fn(x, y) {
    x + y;
};

let result = add(12, 34);
FN;

        $expectedTokenTypes = [
            [TokenType::Let, 'let'],
            [TokenType::Identifier, 'add'],
            [TokenType::Assign, '='],
            [TokenType::Function, 'fn'],
            [TokenType::Lparen, '('],
            [TokenType::Identifier, 'x'],
            [TokenType::Comma, ','],
            [TokenType::Identifier, 'y'],
            [TokenType::Rparen, ')'],
            [TokenType::Lbrace, '{'],
            [TokenType::Identifier, 'x'],
            [TokenType::Plus, '+'],
            [TokenType::Identifier, 'y'],
            [TokenType::Semicolon, ';'],
            [TokenType::Rbrace, '}'],
            [TokenType::Semicolon, ';'],

            [TokenType::Let, 'let'],
            [TokenType::Identifier, 'result'],
            [TokenType::Assign, '='],
            [TokenType::Identifier, 'add'],
            [TokenType::Lparen, '('],
            [TokenType::Int, '12'],
            [TokenType::Comma, ','],
            [TokenType::Int, '34'],
            [TokenType::Rparen, ')'],
            [TokenType::Semicolon, ';'],

            [TokenType::Eof, Char::Nul->value]
        ];

        $this->assertLexer(
            $this->createLexer($input),
            $expectedTokenTypes,
            TokenType::Eof
        );
    }

    public function testCondition(): void
    {
        $input = <<<'CONDITION'
if (5 < 10) {
    return true;
} else {
    return false;
}
CONDITION;

        $expectedTokenTypes = [
            [TokenType::If, 'if'],
            [TokenType::Lparen, '('],
            [TokenType::Int, '5'],
            [TokenType::Lt, '<'],
            [TokenType::Int, '10'],
            [TokenType::Rparen, ')'],
            [TokenType::Lbrace, '{'],
            [TokenType::Return, 'return'],
            [TokenType::True, 'true'],
            [TokenType::Semicolon, ';'],
            [TokenType::Rbrace, '}'],
            [TokenType::Else, 'else'],
            [TokenType::Lbrace, '{'],
            [TokenType::Return, 'return'],
            [TokenType::False, 'false'],
            [TokenType::Semicolon, ';'],
            [TokenType::Rbrace, '}'],

            [TokenType::Eof, Char::Nul->value]
        ];

        $this->assertLexer(
            $this->createLexer($input),
            $expectedTokenTypes,
            TokenType::Eof
        );
    }

    public function testExpr(): void
    {
        $input = '12 / 2 + 100 * 5 - 90 + 3! + 3**2;';

        $expectedTokenTypes = [
            [TokenType::Int, '12'],
            [TokenType::Slash, '/'],
            [TokenType::Int, '2'],
            [TokenType::Plus, '+'],
            [TokenType::Int, '100'],
            [TokenType::Asterisk, '*'],
            [TokenType::Int, '5'],
            [TokenType::Minus, '-'],
            [TokenType::Int, '90'],
            [TokenType::Plus, '+'],
            [TokenType::Int, '3'],
            [TokenType::Bang, '!'],
            [TokenType::Plus, '+'],
            [TokenType::Int, '3'],
            [TokenType::DoubleAsterisk, '**'],
            [TokenType::Int, '2'],
            [TokenType::Semicolon, ';'],

            [TokenType::Eof, Char::Nul->value]
        ];

        $this->assertLexer(
            $this->createLexer($input),
            $expectedTokenTypes,
            TokenType::Eof
        );
    }

    public function testBoolean(): void
    {
        $input = <<<'BOOL'
!true == false;
10 != 9;
1 > -1;
-1 < 1;
BOOL;

        $expectedTokenTypes = [
            [TokenType::Bang, '!'],
            [TokenType::True, 'true'],
            [TokenType::Eq, '=='],
            [TokenType::False, 'false'],
            [TokenType::Semicolon, ';'],

            [TokenType::Int, '10'],
            [TokenType::NotEq, '!='],
            [TokenType::Int, '9'],
            [TokenType::Semicolon, ';'],

            [TokenType::Int, '1'],
            [TokenType::Gt, '>'],
            [TokenType::Minus, '-'],
            [TokenType::Int, '1'],
            [TokenType::Semicolon, ';'],

            [TokenType::Minus, '-'],
            [TokenType::Int, '1'],
            [TokenType::Lt, '<'],
            [TokenType::Int, '1'],
            [TokenType::Semicolon, ';'],

            [TokenType::Eof, Char::Nul->value]
        ];

        $this->assertLexer(
            $this->createLexer($input),
            $expectedTokenTypes,
            TokenType::Eof
        );
    }

    public function testStringNotEnclosed(): void
    {
        try {
            $this->assertLexer($this->createLexer('"a\"'), [], TokenType::Eof);
            self::fail('Expected LexerException');
        } catch (LexerException $lexerException) {
            self::assertSame(LexerExceptionType::StringMustBeEnclosed, $lexerException->getType());
        }
    }

    public function testColon(): void
    {
        $input = '{ "foo": "bar" }';

        $expectedTokenTypes = [
            [TokenType::Lbrace, '{'],
            [TokenType::String, 'foo'],
            [TokenType::Colon, ':'],
            [TokenType::String, 'bar'],
            [TokenType::Rbrace, '}'],

            [TokenType::Eof, Char::Nul->value]
        ];

        $this->assertLexer(
            $this->createLexer($input),
            $expectedTokenTypes,
            TokenType::Eof
        );
    }

    public function testPosition(): void
    {
        $input = <<<'INPUT'
let myVar = 42;

return 41;
INPUT;

        $expectedList = [
            ['line' => 0, 'col' => 0, 'pos' => 3],
            ['line' => 0, 'col' => 4, 'pos' => 9],
            ['line' => 0, 'col' => 10, 'pos' => 11],
            ['line' => 0, 'col' => 12, 'pos' => 14],
            ['line' => 0, 'col' => 14, 'pos' => 15],
            ['line' => 2, 'col' => 0, 'pos' => 23],
            ['line' => 2, 'col' => 7, 'pos' => 26],
            ['line' => 2, 'col' => 9, 'pos' => 27],
            ['line' => 2, 'col' => 10, 'pos' => 28],
        ];

        $lexer = $this->createLexer($input);
        $lexer->rewind();
        foreach ($expectedList as $expected) {
            self::assertSame($expected['line'], $lexer->current()->debug->line);
            self::assertSame($expected['col'], $lexer->current()->debug->col);
            self::assertSame($expected['pos'], $lexer->key());
            $lexer->next();
        }
    }


    /**
     * @return string[][]
     */
    public static function stringProvider(): array
    {
        return [
            ['"foo"', 'foo'],
            ['"fo\ao"', 'fo\ao'],
            ['"fo\"o"', 'fo"o'],
        ];
    }

    /**
     * @param array{TokenType, string}[] $expectedTokenTypes
     */
    private function assertLexer(LexerInterface $lexer, array $expectedTokenTypes, TokenType $stopBy): void
    {
        $iteration = 0;
        $expectedIteration = count($expectedTokenTypes) - 1;
        foreach ($lexer as $token) {
            $expectedTokenType = array_shift($expectedTokenTypes);

            if (!isset($expectedTokenType[0], $expectedTokenType[1])) {
                self::fail('Wrong format for argument $expectedTokenType.');
            }

            self::assertSame($expectedTokenType[0], $token->type);
            self::assertSame($expectedTokenType[1], $token->value);
            ++$iteration;
        }

        self::assertSame($expectedIteration, $iteration);
        self::assertSame($stopBy, $lexer->current()->type);
    }
}
