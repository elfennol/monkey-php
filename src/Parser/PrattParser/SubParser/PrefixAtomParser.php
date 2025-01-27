<?php

declare(strict_types=1);

namespace Elfennol\MonkeyPhp\Parser\PrattParser\SubParser;

use Elfennol\MonkeyPhp\Lexer\LexerInterface;
use Elfennol\MonkeyPhp\Node\ExprNodeInterface;
use Elfennol\MonkeyPhp\Parser\Eater\TokenEater;
use Elfennol\MonkeyPhp\Parser\ExprParserInterface;

readonly class PrefixAtomParser implements PrefixParserInterface
{
    public function __construct(private TokenEater $tokenEater, private PrefixAtomBuilder $builder)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function parse(LexerInterface $lexer, int $rightBp, ExprParserInterface $exprParser): ExprNodeInterface
    {
        $left = $this->builder->createNode($lexer);
        $this->tokenEater->eat($lexer->current()->type, $lexer);

        return $left;
    }
}
