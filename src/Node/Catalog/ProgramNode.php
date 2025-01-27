<?php

declare(strict_types=1);

namespace Elfennol\MonkeyPhp\Node\Catalog;

use Elfennol\MonkeyPhp\Node\NodeType;
use Elfennol\MonkeyPhp\Node\StmtListNodeInterface;
use Elfennol\MonkeyPhp\Node\StmtNodeInterface;
use Elfennol\MonkeyPhp\Utils\Json\JsonKey;

readonly class ProgramNode implements StmtListNodeInterface
{
    /**
     * @param StmtNodeInterface[] $stmts
     */
    public function __construct(private array $stmts)
    {
    }

    public function type(): NodeType
    {
        return NodeType::Program;
    }

    public function debug(): array
    {
        return ['node' => $this->type()];
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
        foreach ($this->stmts as $child) {
            $decoded[$key][] = $child;
        }

        return $decoded;
    }
}
