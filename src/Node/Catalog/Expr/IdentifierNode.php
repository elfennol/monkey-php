<?php

declare(strict_types=1);

namespace Elfennol\MonkeyPhp\Node\Catalog\Expr;

use Elfennol\MonkeyPhp\Node\CallableInterface;
use Elfennol\MonkeyPhp\Node\ExprNodeInterface;
use Elfennol\MonkeyPhp\Node\NodeType;
use Elfennol\MonkeyPhp\Token\Token;
use Elfennol\MonkeyPhp\Utils\Json\JsonKey;

readonly class IdentifierNode implements ExprNodeInterface, CallableInterface
{
    public function __construct(private Token $token)
    {
    }

    public function type(): NodeType
    {
        return NodeType::Identifier;
    }

    public function debug(): array
    {
        return ['node' => $this->type(), 'nearToken' => $this->token, 'nodeName' => $this->name()];
    }

    public function name(): string
    {
        return $this->token->value;
    }

    /**
     * @return array<string, array{}>
     */
    public function jsonSerialize(): array
    {
        return [(new JsonKey($this->name()))->key => []];
    }
}
