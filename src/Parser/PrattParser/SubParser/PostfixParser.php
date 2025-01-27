<?php

declare(strict_types=1);

namespace Elfennol\MonkeyPhp\Parser\PrattParser\SubParser;

use Elfennol\MonkeyPhp\Lexer\LexerInterface;
use Elfennol\MonkeyPhp\Node\Catalog\Expr\PostfixOpNode;
use Elfennol\MonkeyPhp\Node\ExprNodeInterface;
use Elfennol\MonkeyPhp\Parser\Eater\TokenEater;

readonly class PostfixParser implements PostfixParserInterface
{
    public function __construct(private TokenEater $tokenEater)
    {
    }

    public function parse(LexerInterface $lexer, ExprNodeInterface $left): ExprNodeInterface
    {
        $operator = $lexer->current();
        $this->tokenEater->eat($operator->type, $lexer);

        return new PostfixOpNode($operator, $left);
    }
}
