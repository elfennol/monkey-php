<?php

declare(strict_types=1);

namespace Elfennol\MonkeyPhp\Lexer;

use Elfennol\MonkeyPhp\Utils\String\StringIterator;

interface LexerBuilderInterface
{
    public function build(StringIterator $input): Lexer;
}
