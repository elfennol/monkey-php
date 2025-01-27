<?php

declare(strict_types=1);

namespace Elfennol\MonkeyPhp\Node\Catalog\Atom;

use Elfennol\MonkeyPhp\Node\AtomExprNodeInterface;
use Elfennol\MonkeyPhp\Node\NodeType;
use Elfennol\MonkeyPhp\Token\Token;
use Elfennol\MonkeyPhp\Utils\Json\JsonKey;

/**
 * @internal
 */
abstract readonly class AtomNode implements AtomExprNodeInterface
{
    public function __construct(private Token $token)
    {
    }

    abstract public function type(): NodeType;

    public function debug(): array
    {
        return ['node' => $this->type(), 'nearToken' => $this->token, 'nodeValue' => $this->value()];
    }

    public function value(): string
    {
        return $this->token->value;
    }

    /**
     * @return array<string, array{}>
     */
    public function jsonSerialize(): array
    {
        return [(new JsonKey($this->value()))->key => []];
    }
}
