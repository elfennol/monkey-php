<?php

declare(strict_types=1);

namespace Elfennol\MonkeyPhp\Parser;

use Elfennol\MonkeyPhp\Lexer\LexerInterface;
use Elfennol\MonkeyPhp\Node\Catalog\ProgramNode;
use Elfennol\MonkeyPhp\Node\NodeInterface;
use Elfennol\MonkeyPhp\Node\StmtNodeInterface;
use Elfennol\MonkeyPhp\Token\TokenType;

readonly class Parser implements ParserInterface
{
    public function __construct(
        private StmtParserInterface $stmtParser,
        private ExprParserInterface $exprParser,
    ) {
    }

    public function parse(LexerInterface $lexer): NodeInterface
    {
        $lexer->rewind();

        return $this->program($lexer);
    }

    private function program(LexerInterface $lexer): NodeInterface
    {
        return new ProgramNode($this->stmts($lexer));
    }

    /**
     * @return StmtNodeInterface[]
     */
    private function stmts(LexerInterface $lexer): array
    {
        $stmts = [];
        while (TokenType::Eof !== $lexer->current()->type) {
            $stmts[] = $this->stmtParser->parse($lexer, $this->exprParser);
        }

        return $stmts;
    }
}
