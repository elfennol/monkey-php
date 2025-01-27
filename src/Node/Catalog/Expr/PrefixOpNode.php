<?php

declare(strict_types=1);

namespace Elfennol\MonkeyPhp\Node\Catalog\Expr;

use Elfennol\MonkeyPhp\Node\ExprNodeInterface;
use Elfennol\MonkeyPhp\Node\NodeType;
use Elfennol\MonkeyPhp\Node\OpNodeInterface;
use Elfennol\MonkeyPhp\Token\Token;
use Elfennol\MonkeyPhp\Token\TokenType;
use Elfennol\MonkeyPhp\Utils\Json\JsonKey;

readonly class PrefixOpNode implements OpNodeInterface
{
    public function __construct(
        private Token $token,
        private ExprNodeInterface $operand,
    ) {
    }

    public function type(): NodeType
    {
        return NodeType::PrefixOp;
    }

    public function debug(): array
    {
        return ['node' => $this->type(), 'nearToken' => $this->token, 'nodeOperator' => $this->operator()];
    }

    public function operand(): ExprNodeInterface
    {
        return $this->operand;
    }

    public function operator(): TokenType
    {
        return $this->token->type;
    }

    /**
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        $key = (new JsonKey($this->operator()->value))->key;

        return [$key => [$this->operand]];
    }
}
