<?php

declare(strict_types=1);

namespace Elfennol\MonkeyPhp\Parser;

use Elfennol\MonkeyPhp\Lexer\LexerInterface;
use Elfennol\MonkeyPhp\Node\StmtNodeInterface;

interface StmtParserInterface
{
    public function parse(LexerInterface $lexer, ExprParserInterface $exprParser): StmtNodeInterface;
}
