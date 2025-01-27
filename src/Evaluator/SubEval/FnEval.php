<?php

declare(strict_types=1);

namespace Elfennol\MonkeyPhp\Evaluator\SubEval;

use Elfennol\MonkeyPhp\Evaluator\ContextInterface;
use Elfennol\MonkeyPhp\Node\Catalog\Expr\FnNode;
use Elfennol\MonkeyPhp\SysObject\Catalog\FnSysObject;
use Elfennol\MonkeyPhp\SysObject\SysObjectInterface;

readonly class FnEval
{
    public function evaluate(FnNode $node, ContextInterface $context): SysObjectInterface
    {
        return new FnSysObject($node->fnParams()->identifiers(), $node->body(), $context->env());
    }
}
