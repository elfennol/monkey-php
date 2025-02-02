<?php

declare(strict_types=1);

namespace Elfennol\MonkeyPhp\Node\Catalog\Expr;

use Elfennol\MonkeyPhp\Node\NodeInterface;
use Elfennol\MonkeyPhp\Node\NodeType;
use Elfennol\MonkeyPhp\Token\Token;
use Elfennol\MonkeyPhp\Utils\Json\JsonKey;

readonly class FnParamsNode implements NodeInterface
{
    public function __construct(
        private Token $token,
        /** @var IdentifierNode[] */
        private array $identifiers,
    ) {
    }

    public function type(): NodeType
    {
        return NodeType::FnParams;
    }

    public function debug(): array
    {
        return ['node' => $this->type(), 'nearToken' => $this->token];
    }

    /**
     * @return IdentifierNode[]
     */
    public function identifiers(): array
    {
        return $this->identifiers;
    }

    /**
     * @param IdentifierNode[] $identifiers
     */
    public function buildWith(array $identifiers): FnParamsNode
    {
        return new FnParamsNode($this->token, $identifiers);
    }

    /**
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        $key = (new JsonKey($this->type()->name))->key;
        $decoded = [$key => []];
        foreach ($this->identifiers as $fnParam) {
            $decoded[$key][] = $fnParam;
        }

        return $decoded;
    }
}
