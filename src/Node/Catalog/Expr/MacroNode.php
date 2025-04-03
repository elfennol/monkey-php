<?php

declare(strict_types=1);

namespace Elfennol\MonkeyPhp\Node\Catalog\Expr;

use Elfennol\MonkeyPhp\Node\Catalog\BlockNode;
use Elfennol\MonkeyPhp\Node\ExprNodeInterface;
use Elfennol\MonkeyPhp\Node\NodeType;
use Elfennol\MonkeyPhp\Token\Token;
use Elfennol\MonkeyPhp\Utils\Json\JsonKey;

readonly class MacroNode implements ExprNodeInterface
{
    public function __construct(
        private Token $token,
        private FnParamsNode $fnParams,
        private BlockNode $body,
    ) {
    }

    public function type(): NodeType
    {
        return NodeType::Macro;
    }

    public function debug(): array
    {
        return ['node' => $this->type(), 'nearToken' => $this->token];
    }

    public function fnParams(): FnParamsNode
    {
        return $this->fnParams;
    }

    public function body(): BlockNode
    {
        return $this->body;
    }

    /**
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        $key = (new JsonKey($this->type()->name))->key;

        return [
            $key => [
                $this->fnParams,
                $this->body,
            ]
        ];
    }
}
