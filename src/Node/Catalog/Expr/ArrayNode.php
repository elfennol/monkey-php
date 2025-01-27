<?php

declare(strict_types=1);

namespace Elfennol\MonkeyPhp\Node\Catalog\Expr;

use Elfennol\MonkeyPhp\Node\ExprNodeInterface;
use Elfennol\MonkeyPhp\Node\NodeType;
use Elfennol\MonkeyPhp\Token\Token;
use Elfennol\MonkeyPhp\Utils\Json\JsonKey;

readonly class ArrayNode implements ExprNodeInterface
{
    public function __construct(
        private Token $token,
        /** @var ExprNodeInterface[] */
        private array $elements,
    ) {
    }

    public function type(): NodeType
    {
        return NodeType::Array;
    }

    public function debug(): array
    {
        return ['node' => $this->type(), 'nearToken' => $this->token];
    }

    /**
     * @return ExprNodeInterface[]
     */
    public function elements(): array
    {
        return $this->elements;
    }

    /**
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        $key = (new JsonKey($this->type()->name))->key;
        $decoded = [$key => []];
        foreach ($this->elements as $elements) {
            $decoded[$key][] = $elements;
        }

        return $decoded;
    }
}
