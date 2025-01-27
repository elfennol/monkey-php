<?php

declare(strict_types=1);

namespace Elfennol\MonkeyPhp\Evaluator\SubEval;

use Elfennol\MonkeyPhp\Evaluator\ContextInterface;
use Elfennol\MonkeyPhp\Evaluator\EvaluatorInterface;
use Elfennol\MonkeyPhp\Node\Catalog\BlockNode;
use Elfennol\MonkeyPhp\Node\StmtListNodeInterface;
use Elfennol\MonkeyPhp\SysObject\Catalog\UnitSysObject;
use Elfennol\MonkeyPhp\SysObject\SysObjectInterface;
use Elfennol\MonkeyPhp\SysObject\TerminusSysObjectInterface;

readonly class StmtListEval
{
    public function evaluate(
        EvaluatorInterface $evaluator,
        StmtListNodeInterface $node,
        ContextInterface $context,
    ): SysObjectInterface {
        $result = new UnitSysObject();

        foreach ($node->stmts() as $stmt) {
            $result = $evaluator->evaluate($stmt, $context);

            if ($result instanceof TerminusSysObjectInterface) {
                if ($node instanceof BlockNode) {
                    return $result;
                }

                return $result->value();
            }
        }

        return $result;
    }
}
