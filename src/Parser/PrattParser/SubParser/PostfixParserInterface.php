<?php

namespace Elfennol\MonkeyPhp\Parser\PrattParser\SubParser;

use Elfennol\MonkeyPhp\Lexer\LexerInterface;
use Elfennol\MonkeyPhp\Node\ExprNodeInterface;

/**
 * @internal
 */
interface PostfixParserInterface
{
    public function parse(LexerInterface $lexer, ExprNodeInterface $left): ExprNodeInterface;
}
