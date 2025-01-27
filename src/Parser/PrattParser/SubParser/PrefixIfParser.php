<?php

declare(strict_types=1);

namespace Elfennol\MonkeyPhp\Parser\PrattParser\SubParser;

use Elfennol\MonkeyPhp\Lexer\LexerInterface;
use Elfennol\MonkeyPhp\Node\Catalog\BlockNode;
use Elfennol\MonkeyPhp\Node\Catalog\Expr\IfNode;
use Elfennol\MonkeyPhp\Node\ExprNodeInterface;
use Elfennol\MonkeyPhp\Parser\Eater\TokenEater;
use Elfennol\MonkeyPhp\Parser\ExprParserInterface;
use Elfennol\MonkeyPhp\Parser\SubParser\BlockStmtParser;
use Elfennol\MonkeyPhp\Token\TokenType;
use Elfennol\MonkeyPhp\Utils\Option\None;
use Elfennol\MonkeyPhp\Utils\Option\Some;

readonly class PrefixIfParser implements PrefixParserInterface
{
    public function __construct(
        private TokenEater $tokenEater,
        private BlockStmtParser $blockStmtParser,
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public function parse(LexerInterface $lexer, int $rightBp, ExprParserInterface $exprParser): ExprNodeInterface
    {
        $nearToken = $lexer->current();
        $this->tokenEater->eat(TokenType::If, $lexer);
        $this->tokenEater->eat(TokenType::Lparen, $lexer);
        $condition = $exprParser->parse($lexer, 0);
        $this->tokenEater->eat(TokenType::Rparen, $lexer);

        $consequence = new BlockNode($nearToken, $this->blockStmtParser->parse($lexer, $exprParser));

        $alternative = new None();
        if (TokenType::Else === $lexer->current()->type) {
            $this->tokenEater->eat(TokenType::Else, $lexer);
            $elseBlock = $this->blockStmtParser->parse($lexer, $exprParser);
            $alternative = new Some(new BlockNode($nearToken, $elseBlock));
        }

        return new IfNode($nearToken, $condition, $consequence, $alternative);
    }
}
