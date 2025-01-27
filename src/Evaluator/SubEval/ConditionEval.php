<?php

declare(strict_types=1);

namespace Elfennol\MonkeyPhp\Evaluator\SubEval;

use Elfennol\MonkeyPhp\Evaluator\ContextInterface;
use Elfennol\MonkeyPhp\Evaluator\EvaluatorException;
use Elfennol\MonkeyPhp\Evaluator\EvaluatorExceptionType;
use Elfennol\MonkeyPhp\Evaluator\EvaluatorInterface;
use Elfennol\MonkeyPhp\Evaluator\RefSysObject;
use Elfennol\MonkeyPhp\Node\ConditionalNodeInterface;
use Elfennol\MonkeyPhp\SysObject\Catalog\BoolSysObject;
use Elfennol\MonkeyPhp\SysObject\Catalog\UnitSysObject;
use Elfennol\MonkeyPhp\SysObject\SysObjectInterface;

readonly class ConditionEval
{
    public function __construct(private RefSysObject $ref)
    {
    }

    public function evaluate(
        EvaluatorInterface $evaluator,
        ConditionalNodeInterface $node,
        ContextInterface $context
    ): SysObjectInterface {
        $condition = $evaluator->evaluate($node->expr(), $context);

        if (!$condition instanceof BoolSysObject) {
            throw new EvaluatorException(
                EvaluatorExceptionType::ConditionInvalid,
                ['sysObjectType' => $condition->type(), 'node' => $node->debug()],
                'Condition must be boolean.'
            );
        }

        if ($this->ref->true === $condition) {
            return $evaluator->evaluate($node->consequence(), $context);
        }

        if ($node->alternative()->isSome()) {
            return $evaluator->evaluate($node->alternative()->unwrap(), $context);
        }

        return new UnitSysObject();
    }
}
