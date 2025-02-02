<?php

declare(strict_types=1);

namespace Elfennol\MonkeyPhp\Node\Catalog\Expr;

use Elfennol\MonkeyPhp\Node\Catalog\BlockNode;
use Elfennol\MonkeyPhp\Node\ConditionalNodeInterface;
use Elfennol\MonkeyPhp\Node\ExprNodeInterface;
use Elfennol\MonkeyPhp\Node\NodeType;
use Elfennol\MonkeyPhp\Token\Token;
use Elfennol\MonkeyPhp\Utils\Json\JsonKey;
use Elfennol\MonkeyPhp\Utils\Option\Option;

readonly class IfNode implements ConditionalNodeInterface
{
    public function __construct(
        private Token $token,
        private ExprNodeInterface $expr,
        private BlockNode $consequence,
        /** @var Option<BlockNode> */
        private Option $alternative,
    ) {
    }

    public function type(): NodeType
    {
        return NodeType::IfNode;
    }

    public function debug(): array
    {
        return ['node' => $this->type(), 'nearToken' => $this->token];
    }

    public function expr(): ExprNodeInterface
    {
        return $this->expr;
    }

    public function consequence(): BlockNode
    {
        return $this->consequence;
    }

    /**
     * @return Option<BlockNode>
     */
    public function alternative(): Option
    {
        return $this->alternative;
    }

    /**
     * @param Option<BlockNode> $alternative
     */
    public function buildWith(ExprNodeInterface $expr, BlockNode $consequence, Option $alternative): IfNode
    {
        return new IfNode($this->token, $expr, $consequence, $alternative);
    }

    /**
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        $key = (new JsonKey($this->type()->name))->key;
        $decoded = [$key => []];
        $decoded[$key][] = $this->expr;
        $decoded[$key][] = $this->consequence;
        if ($this->alternative->isSome()) {
            $decoded[$key][] = $this->alternative->unwrap();
        }

        return $decoded;
    }
}
