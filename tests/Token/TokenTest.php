<?php

declare(strict_types=1);

namespace Elfennol\MonkeyPhp\Tests\Token;

use Elfennol\MonkeyPhp\Token\Token;
use Elfennol\MonkeyPhp\Token\TokenType;
use PHPUnit\Framework\TestCase;

class TokenTest extends TestCase
{
    public function testJsonSerialize(): void
    {
        $expected = <<<'JSON'
{
  "type": {
    "name": "String"
  },
  "value": "foo",
  "debug": {
    "line": 1,
    "col": 2
  }
}
JSON;

        self::assertJsonStringEqualsJsonString(
            $expected,
            (string)json_encode(new Token(TokenType::String, 'foo', 1, 2))
        );
    }
}
