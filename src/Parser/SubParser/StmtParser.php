<?php

declare(strict_types=1);

namespace Elfennol\MonkeyPhp\Parser\SubParser;

use Elfennol\MonkeyPhp\Lexer\LexerInterface;
use Elfennol\MonkeyPhp\Node\StmtNodeInterface;
use Elfennol\MonkeyPhp\Parser\Eater\TokenEater;
use Elfennol\MonkeyPhp\Parser\ExprParserInterface;
use Elfennol\MonkeyPhp\Parser\PrattParser\BindingPowerSet;
use Elfennol\MonkeyPhp\Parser\StmtParserInterface;
use Elfennol\MonkeyPhp\Token\TokenType;

readonly class StmtParser implements StmtParserInterface
{
    public function __construct(
        private TokenEater $tokenEater,
        private LetParser $letParser,
        private ReturnParser $returnParser,
    ) {
    }

    public function parse(LexerInterface $lexer, ExprParserInterface $exprParser): StmtNodeInterface
    {
        $stmtType = $lexer->current()->type;
        $stmt = match ($stmtType) {
            TokenType::Let => $this->letParser->parse($lexer, $exprParser),
            TokenType::Return => $this->returnParser->parse($lexer, $exprParser),
            default => $exprParser->parse($lexer, BindingPowerSet::MIN),
        };

        if (in_array($stmtType, [TokenType::Let, TokenType::Return], true)
            ||
            TokenType::Rbrace !== $lexer->current()->type) {
            $this->tokenEater->eat(TokenType::Semicolon, $lexer);
        }

        return $stmt;
    }
}
