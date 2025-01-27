<?php

declare(strict_types=1);

namespace Elfennol\MonkeyPhp\Node\Catalog\Stmt;

use Elfennol\MonkeyPhp\Node\Catalog\Expr\IdentifierNode;
use Elfennol\MonkeyPhp\Node\ExprNodeInterface;
use Elfennol\MonkeyPhp\Node\NodeType;
use Elfennol\MonkeyPhp\Node\StmtNodeInterface;
use Elfennol\MonkeyPhp\Token\Token;
use Elfennol\MonkeyPhp\Utils\Json\JsonKey;

readonly class LetNode implements StmtNodeInterface
{
    public function __construct(
        private Token $token,
        private IdentifierNode $identifierNode,
        private ExprNodeInterface $exprNode
    ) {
    }

    public function type(): NodeType
    {
        return NodeType::Let;
    }

    public function debug(): array
    {
        return ['node' => $this->type(), 'nearToken' => $this->token];
    }

    public function expr(): ExprNodeInterface
    {
        return $this->exprNode;
    }

    public function identifier(): IdentifierNode
    {
        return $this->identifierNode;
    }

    /**
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        return [
            (new JsonKey($this->type()->name))->key => [
                $this->identifierNode,
                $this->exprNode,
            ]
        ];
    }
}
