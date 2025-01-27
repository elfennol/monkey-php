<?php

declare(strict_types=1);

namespace Elfennol\MonkeyPhp\Parser;

use Elfennol\MonkeyPhp\Lexer\LexerInterface;
use Elfennol\MonkeyPhp\Node\ExprNodeInterface;

interface ExprParserInterface
{
    public function parse(LexerInterface $lexer, int $startingBp): ExprNodeInterface;
}
