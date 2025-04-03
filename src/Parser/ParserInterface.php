<?php

declare(strict_types=1);

namespace Elfennol\MonkeyPhp\Parser;

use Elfennol\MonkeyPhp\Lexer\LexerInterface;
use Elfennol\MonkeyPhp\Node\ProgramNodeInterface;

interface ParserInterface
{
    public function parse(LexerInterface $lexer): ProgramNodeInterface;
}
