<?php

declare(strict_types=1);

namespace Elfennol\MonkeyPhp\Node\Catalog\Expr;

use Elfennol\MonkeyPhp\Node\CallableExprInterface;
use Elfennol\MonkeyPhp\Node\ExprNodeInterface;
use Elfennol\MonkeyPhp\Node\NodeType;
use Elfennol\MonkeyPhp\Token\Token;
use Elfennol\MonkeyPhp\Utils\Json\JsonKey;
use Elfennol\MonkeyPhp\Utils\Option\None;
use Elfennol\MonkeyPhp\Utils\Option\Option;
use Elfennol\MonkeyPhp\Utils\Option\Some;

readonly class FnCallNode implements ExprNodeInterface
{
    /**
     * @param ExprNodeInterface[] $fnArgs
     */
    public function __construct(
        private Token $token,
        private CallableExprInterface $fnExpr,
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

    public function fnExpr(): CallableExprInterface
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
     * @return Option<IdentifierNode>
     */
    public function identifier(): Option
    {
        return $this->fnExpr instanceof IdentifierNode ? new Some($this->fnExpr) : new None();
    }

    /**
     * @param ExprNodeInterface[] $fnArgs
     */
    public function buildWith(CallableExprInterface $fnExpr, array $fnArgs): FnCallNode
    {
        return new FnCallNode($this->token, $fnExpr, $fnArgs);
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
