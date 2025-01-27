<?php

declare(strict_types=1);

namespace Elfennol\MonkeyPhp\Node\Catalog\Expr;

use Elfennol\MonkeyPhp\Node\CallableInterface;
use Elfennol\MonkeyPhp\Node\ExprNodeInterface;
use Elfennol\MonkeyPhp\Node\NodeType;
use Elfennol\MonkeyPhp\Token\Token;
use Elfennol\MonkeyPhp\Utils\Json\JsonKey;

readonly class FnCallNode implements ExprNodeInterface
{
    /**
     * @param ExprNodeInterface[] $fnArgs
     */
    public function __construct(
        private Token $token,
        private CallableInterface&ExprNodeInterface $fnExpr,
        private array $fnArgs,
    ) {
    }

    public function type(): NodeType
    {
        return NodeType::FnCall;
    }

    public function debug(): array
    {
        return ['node' => $this->type(), 'nearToken' => $this->token];
    }

    public function fnExpr(): CallableInterface&ExprNodeInterface
    {
        return $this->fnExpr;
    }

    /**
     * @return ExprNodeInterface[]
     */
    public function fnArgs(): array
    {
        return $this->fnArgs;
    }

    /**
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        $key = (new JsonKey($this->type()->name))->key;
        $decoded = [$key => []];
        $decoded[$key][] = $this->fnExpr;
        foreach ($this->fnArgs as $arg) {
            $decoded[$key][] = $arg;
        }

        return $decoded;
    }
}
