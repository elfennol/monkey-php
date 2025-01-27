<?php

declare(strict_types=1);

namespace Elfennol\MonkeyPhp\Node\Catalog\Stmt;

use Elfennol\MonkeyPhp\Node\ExprNodeInterface;
use Elfennol\MonkeyPhp\Node\NodeType;
use Elfennol\MonkeyPhp\Node\StmtNodeInterface;
use Elfennol\MonkeyPhp\Token\Token;
use Elfennol\MonkeyPhp\Utils\Json\JsonKey;
use Elfennol\MonkeyPhp\Utils\Option\Option;

readonly class ReturnNode implements StmtNodeInterface
{
    /**
     * @param Option<ExprNodeInterface> $exprNode
     */
    public function __construct(private Token $token, private Option $exprNode)
    {
    }

    public function type(): NodeType
    {
        return NodeType::Return;
    }

    public function debug(): array
    {
        return ['node' => $this->type(), 'nearToken' => $this->token];
    }

    /**
     * @return Option<ExprNodeInterface>
     */
    public function expr(): Option
    {
        return $this->exprNode;
    }

    /**
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        if ($this->exprNode->isSome()) {
            return [
                (new JsonKey($this->type()->name))->key => [
                    $this->exprNode->unwrap(),
                ]
            ];
        }

        return [
            (new JsonKey($this->type()->name))->key => []
        ];
    }
}
