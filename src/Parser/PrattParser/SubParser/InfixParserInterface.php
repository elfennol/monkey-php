<?php

declare(strict_types=1);

namespace Elfennol\MonkeyPhp\Parser\PrattParser\SubParser;

use Elfennol\MonkeyPhp\Lexer\LexerInterface;
use Elfennol\MonkeyPhp\Node\ExprNodeInterface;
use Elfennol\MonkeyPhp\Parser\ExprParserInterface;

/**
 * @internal
 */
interface InfixParserInterface
{
    public function parse(
        LexerInterface $lexer,
        int $currentBp,
        ExprNodeInterface $left,
        ExprParserInterface $exprParser
    ): ExprNodeInterface;
}
