<?php

declare(strict_types=1);

namespace Elfennol\MonkeyPhp\SysObject\Catalog;

use Elfennol\MonkeyPhp\Node\Catalog\BlockNode;
use Elfennol\MonkeyPhp\Node\Catalog\Expr\IdentifierNode;
use Elfennol\MonkeyPhp\SysObject\Context\EnvInterface;
use Elfennol\MonkeyPhp\SysObject\SysObjectInterface;
use Elfennol\MonkeyPhp\SysObject\SysObjectType;

readonly class FnSysObject implements SysObjectInterface
{
    public function __construct(
        /** @var IdentifierNode[] */
        private array $params,
        private BlockNode $body,
        private EnvInterface $env,
    ) {
    }

    public function type(): SysObjectType
    {
        return SysObjectType::Function;
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

    public function env(): EnvInterface
    {
        return $this->env;
    }
}
