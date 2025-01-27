<?php

declare(strict_types=1);

namespace Elfennol\MonkeyPhp\Parser\SubParser;

use Elfennol\MonkeyPhp\Lexer\LexerInterface;
use Elfennol\MonkeyPhp\Node\StmtNodeInterface;
use Elfennol\MonkeyPhp\Parser\Eater\TokenEater;
use Elfennol\MonkeyPhp\Parser\ExprParserInterface;
use Elfennol\MonkeyPhp\Token\TokenType;

readonly class BlockStmtParser
{
    public function __construct(private TokenEater $tokenEater, private StmtParser $stmtParser)
    {
    }

    /**
     * @return StmtNodeInterface[]
     */
    public function parse(LexerInterface $lexer, ExprParserInterface $exprParser): array
    {
        $this->tokenEater->eat(TokenType::Lbrace, $lexer);
        $stmts = [];
        while (!in_array($lexer->current()->type, [TokenType::Eof, TokenType::Rbrace], true)) {
            $stmts[] = $this->stmtParser->parse($lexer, $exprParser);
        }
        $this->tokenEater->eat(TokenType::Rbrace, $lexer);

        return $stmts;
    }
}
