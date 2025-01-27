<?php

declare(strict_types=1);

namespace Elfennol\MonkeyPhp\Parser\PrattParser\SubParser;

use Elfennol\MonkeyPhp\Lexer\LexerInterface;
use Elfennol\MonkeyPhp\Node\Catalog\Atom\BoolNode;
use Elfennol\MonkeyPhp\Node\Catalog\Atom\IntNode;
use Elfennol\MonkeyPhp\Node\Catalog\Atom\StringNode;
use Elfennol\MonkeyPhp\Node\Catalog\Expr\IdentifierNode;
use Elfennol\MonkeyPhp\Node\ExprNodeInterface;
use Elfennol\MonkeyPhp\Parser\ParserException;
use Elfennol\MonkeyPhp\Parser\ParserExceptionType;
use Elfennol\MonkeyPhp\Token\TokenType;

readonly class PrefixAtomBuilder
{
    public function createNode(LexerInterface $lexer): ExprNodeInterface
    {
        return match ($lexer->current()->type) {
            TokenType::Identifier => new IdentifierNode($lexer->current()),
            TokenType::Int => new IntNode($lexer->current()),
            TokenType::True, TokenType::False => new BoolNode($lexer->current()),
            TokenType::String => new StringNode($lexer->current()),
            default => throw new ParserException(
                ParserExceptionType::TokenMustBeAtom,
                ['currentToken' => $lexer->current()->normalize()],
                'Unable to parse atom expression.'
            )
        };
    }
}
