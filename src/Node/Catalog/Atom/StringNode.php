<?php

declare(strict_types=1);

namespace Elfennol\MonkeyPhp\Node\Catalog\Atom;

use Elfennol\MonkeyPhp\Node\AtomExprNodeInterface;
use Elfennol\MonkeyPhp\Node\NodeType;
use Elfennol\MonkeyPhp\Token\Token;
use Elfennol\MonkeyPhp\Utils\Json\JsonKey;

readonly class StringNode implements AtomExprNodeInterface
{
    public function __construct(private Token $token)
    {
    }

    public function type(): NodeType
    {
        return NodeType::String;
    }

    public function debug(): array
    {
        return ['node' => $this->type(), 'nearToken' => $this->token, 'nodeValue' => $this->value()];
    }

    public function value(): string
    {
        return $this->token->value;
    }

    public function buildWith(string $value): StringNode
    {
        return new StringNode($this->token->buildWith($this->token->type, $value));
    }

    /**
     * @return array<string, array{}>
     */
    public function jsonSerialize(): array
    {
        return [(new JsonKey($this->value()))->key => []];
    }
}
