<?php

declare(strict_types=1);

namespace Elfennol\MonkeyPhp\Parser\PrattParser\SubParser;

use Elfennol\MonkeyPhp\Lexer\LexerInterface;
use Elfennol\MonkeyPhp\Node\Catalog\Expr\IdentifierNode;
use Elfennol\MonkeyPhp\Node\ExprNodeInterface;
use Elfennol\MonkeyPhp\Parser\ExprParserInterface;
use Elfennol\MonkeyPhp\Parser\ParserException;
use Elfennol\MonkeyPhp\Parser\ParserExceptionType;
use Elfennol\MonkeyPhp\Parser\SubParser\AssignParser;

readonly class InfixAssignParser implements InfixParserInterface
{
    public function __construct(private AssignParser $assignParser)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function parse(
        LexerInterface $lexer,
        int $currentBp,
        ExprNodeInterface $left,
        ExprParserInterface $exprParser
    ): ExprNodeInterface {
        if (!$left instanceof IdentifierNode) {
            throw new ParserException(ParserExceptionType::NodeTypeInvalid, [
                'currentToken' => $lexer->current()->normalize(),
                'leftNode' => $left->debug(),
            ], 'Unable to parse infix assign expression. Left node must be an identifier.');
        }

        return $this->assignParser->parse($lexer, $exprParser, $left);
    }
}
