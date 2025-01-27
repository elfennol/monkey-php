<?php

declare(strict_types=1);

namespace Elfennol\MonkeyPhp\Parser\PrattParser\SubParser;

use Elfennol\MonkeyPhp\Lexer\LexerInterface;
use Elfennol\MonkeyPhp\Node\Catalog\Expr\PrefixOpNode;
use Elfennol\MonkeyPhp\Node\ExprNodeInterface;
use Elfennol\MonkeyPhp\Parser\Eater\TokenEater;
use Elfennol\MonkeyPhp\Parser\ExprParserInterface;

readonly class PrefixParser implements PrefixParserInterface
{
    public function __construct(private TokenEater $tokenEater)
    {
    }

    public function parse(LexerInterface $lexer, int $rightBp, ExprParserInterface $exprParser): ExprNodeInterface
    {
        $operator = $lexer->current();
        $this->tokenEater->eat($operator->type, $lexer);
        $right = $exprParser->parse($lexer, $rightBp);

        return new PrefixOpNode($operator, $right);
    }
}
