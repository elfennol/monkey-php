<?php

declare(strict_types=1);

namespace Elfennol\MonkeyPhp\Node\Catalog;

use Elfennol\MonkeyPhp\Node\NodeType;
use Elfennol\MonkeyPhp\Node\StmtListNodeInterface;
use Elfennol\MonkeyPhp\Node\StmtNodeInterface;
use Elfennol\MonkeyPhp\Token\Token;
use Elfennol\MonkeyPhp\Utils\Json\JsonKey;

readonly class BlockNode implements StmtListNodeInterface
{
    /**
     * @param StmtNodeInterface[] $stmts
     */
    public function __construct(private Token $token, private array $stmts)
    {
    }

    public function type(): NodeType
    {
        return NodeType::BlockStmt;
    }

    public function debug(): array
    {
        return ['node' => $this->type(), 'nearToken' => $this->token];
    }

    /**
     * @return StmtNodeInterface[]
     */
    public function stmts(): array
    {
        return $this->stmts;
    }

    /**
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        $key = (new JsonKey($this->type()->name))->key;
        $decoded = [$key => []];
        foreach ($this->stmts as $stmt) {
            $decoded[$key][] = $stmt;
        }

        return $decoded;
    }
}
