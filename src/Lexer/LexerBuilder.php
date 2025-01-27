<?php

declare(strict_types=1);

namespace Elfennol\MonkeyPhp\Lexer;

use Elfennol\MonkeyPhp\Utils\String\StringIterator;

readonly class LexerBuilder implements LexerBuilderInterface
{
    public function __construct(
        private TokenBuilder $tokenBuilder,
    ) {
    }

    public function build(StringIterator $input): Lexer
    {
        return new Lexer($input, $this->tokenBuilder);
    }
}
