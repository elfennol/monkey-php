<?php

declare(strict_types=1);

namespace Elfennol\MonkeyPhp\SysObject\Catalog;

use Elfennol\MonkeyPhp\Node\Catalog\BlockNode;
use Elfennol\MonkeyPhp\Node\Catalog\Expr\IdentifierNode;
use Elfennol\MonkeyPhp\SysObject\SysObjectInterface;
use Elfennol\MonkeyPhp\SysObject\SysObjectType;

readonly class MacroSysObject implements SysObjectInterface
{
    public function __construct(
        /** @var IdentifierNode[] */
        private array $params,
        private BlockNode $body,
    ) {
    }

    public function type(): SysObjectType
    {
        return SysObjectType::Macro;
    }

    /**
     * @return IdentifierNode[]
     */
    public function params(): array
    {
        return $this->params;
    }

    public function body(): BlockNode
    {
        return $this->body;
    }
}
