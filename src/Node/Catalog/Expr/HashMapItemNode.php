<?php

declare(strict_types=1);

namespace Elfennol\MonkeyPhp\Node\Catalog\Expr;

use Elfennol\MonkeyPhp\Node\ExprNodeInterface;
use Elfennol\MonkeyPhp\Node\NodeType;
use Elfennol\MonkeyPhp\Token\Token;
use Elfennol\MonkeyPhp\Utils\Json\JsonKey;

readonly class HashMapItemNode implements ExprNodeInterface
{
    public function __construct(
        private Token $token,
        private ExprNodeInterface $key,
        private ExprNodeInterface $value,
    ) {
    }

    public function type(): NodeType
    {
        return NodeType::HashMapItem;
    }

    public function debug(): array
    {
        return ['node' => $this->type(), 'nearToken' => $this->token];
    }

    public function key(): ExprNodeInterface
    {
        return $this->key;
    }

    public function value(): ExprNodeInterface
    {
        return $this->value;
    }

    /**
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        $key = (new JsonKey($this->type()->name))->key;

        return [$key => [$this->key, $this->value]];
    }
}
