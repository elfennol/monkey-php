<?php

declare(strict_types=1);

namespace Elfennol\MonkeyPhp\Tests\Lexer;

use Elfennol\MonkeyPhp\Lexer\LexerException;
use Elfennol\MonkeyPhp\Lexer\LexerExceptionType;
use Elfennol\MonkeyPhp\Lexer\TokenBuilder;
use Elfennol\MonkeyPhp\Lexer\TokenTypeFinder;
use PHPUnit\Framework\TestCase;

class TokenBuilderTest extends TestCase
{
    private TokenBuilder $tokenBuilder;

    protected function setUp(): void
    {
        $this->tokenBuilder = new TokenBuilder(new TokenTypeFinder());
    }

    public function testMakeSpecialStringWhenTokenTypeNotFound(): void
    {
        $value = '🐒';
        $line = 1;
        $col = 1;
        try {
            $this->tokenBuilder->makeSpecialString($value, $line, $col);
            self::fail('Expected LexerException');
        } catch (LexerException $lexerException) {
            self::assertSame(LexerExceptionType::UnableToBuildTheToken, $lexerException->getType());
            self::assertSame(['value' => $value, 'line' => $line, 'col' => $col], $lexerException->getContext());
        }
    }
}
