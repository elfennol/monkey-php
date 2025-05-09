<?php

declare(strict_types=1);

namespace Elfennol\MonkeyPhp\Parser\PrattParser\SubParser;

use Elfennol\MonkeyPhp\Lexer\LexerInterface;
use Elfennol\MonkeyPhp\Node\Catalog\BlockNode;
use Elfennol\MonkeyPhp\Node\Catalog\Expr\FnNode;
use Elfennol\MonkeyPhp\Node\Catalog\Expr\FnParamsNode;
use Elfennol\MonkeyPhp\Node\Catalog\Expr\IdentifierNode;
use Elfennol\MonkeyPhp\Node\Catalog\Expr\MacroNode;
use Elfennol\MonkeyPhp\Node\ExprNodeInterface;
use Elfennol\MonkeyPhp\Parser\Eater\TokenEater;
use Elfennol\MonkeyPhp\Parser\ExprParserInterface;
use Elfennol\MonkeyPhp\Parser\SubParser\BlockStmtParser;
use Elfennol\MonkeyPhp\Token\Token;
use Elfennol\MonkeyPhp\Token\TokenType;

readonly class PrefixFnParser implements PrefixParserInterface
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

        $this->eatNearToken($nearToken, $lexer);

        $fnParams = new FnParamsNode($nearToken, $this->parseFnParams($lexer));
        $block = new BlockNode($nearToken, $this->blockStmtParser->parse($lexer, $exprParser));

        return $this->makeNode($nearToken, $fnParams, $block);
    }

    /**
     * @return IdentifierNode[]
     */
    private function parseFnParams(LexerInterface $lexer): array
    {
        $this->tokenEater->eat(TokenType::Lparen, $lexer);
        $params = [];
        while (TokenType::Rparen !== $lexer->current()->type) {
            $identifier = new IdentifierNode($lexer->current());
            $this->tokenEater->eat(TokenType::Identifier, $lexer);
            $params[] = $identifier;
            /** @phpstan-ignore notIdentical.alwaysTrue */
            if (TokenType::Rparen !== $lexer->current()->type) {
                $this->tokenEater->eat(TokenType::Comma, $lexer);
            }
        }

        $this->tokenEater->eat(TokenType::Rparen, $lexer);

        return $params;
    }

    private function eatNearToken(Token $nearToken, LexerInterface $lexer): void
    {
        if (TokenType::Macro === $nearToken->type) {
            $this->tokenEater->eat(TokenType::Macro, $lexer);

            return;
        }

        $this->tokenEater->eat(TokenType::Function, $lexer);
    }

    private function makeNode(Token $nearToken, FnParamsNode $fnParams, BlockNode $block): ExprNodeInterface
    {
        if (TokenType::Macro === $nearToken->type) {
            return new MacroNode($nearToken, $fnParams, $block);
        }

        return new FnNode($nearToken, $fnParams, $block);
    }
}
