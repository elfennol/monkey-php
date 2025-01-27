<?php

declare(strict_types=1);

namespace Elfennol\MonkeyPhp\Evaluator\SubEval;

use Elfennol\MonkeyPhp\Evaluator\ContextInterface;
use Elfennol\MonkeyPhp\Evaluator\EvaluatorInterface;
use Elfennol\MonkeyPhp\Node\Catalog\Stmt\ReturnNode;
use Elfennol\MonkeyPhp\SysObject\Catalog\ReturnSysObject;
use Elfennol\MonkeyPhp\SysObject\Catalog\UnitSysObject;
use Elfennol\MonkeyPhp\SysObject\SysObjectInterface;

readonly class ReturnEval
{
    public function evaluate(
        EvaluatorInterface $evaluator,
        ReturnNode $node,
        ContextInterface $context
    ): SysObjectInterface {
        if ($node->expr()->isNone()) {
            return new ReturnSysObject(new UnitSysObject());
        }

        return new ReturnSysObject($evaluator->evaluate($node->expr()->unwrap(), $context));
    }
}
