<?php

declare(strict_types=1);

namespace Elfennol\MonkeyPhp\Node\Catalog\Expr;

use Elfennol\MonkeyPhp\Node\ExprNodeInterface;
use Elfennol\MonkeyPhp\Node\NodeType;
use Elfennol\MonkeyPhp\Token\Token;
use Elfennol\MonkeyPhp\Utils\Json\JsonKey;

readonly class IndexExprNode implements ExprNodeInterface
{
    public function __construct(
        private Token $token,
        private ExprNodeInterface $left,
        private ExprNodeInterface $index,
    ) {
    }

    public function type(): NodeType
    {
        return NodeType::IndexExpr;
    }

    public function debug(): array
    {
        return ['node' => $this->type(), 'nearToken' => $this->token];
    }

    public function left(): ExprNodeInterface
    {
        return $this->left;
    }

    public function index(): ExprNodeInterface
    {
        return $this->index;
    }

    public function buildWith(ExprNodeInterface $left, ExprNodeInterface $index): IndexExprNode
    {
        return new IndexExprNode($this->token, $left, $index);
    }

    /**
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        $key = (new JsonKey($this->type()->name))->key;

        return [$key => [$this->left, $this->index]];
    }
}
