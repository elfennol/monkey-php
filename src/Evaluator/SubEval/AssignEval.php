<?php

declare(strict_types=1);

namespace Elfennol\MonkeyPhp\Evaluator\SubEval;

use Elfennol\MonkeyPhp\Evaluator\EvaluatorException;
use Elfennol\MonkeyPhp\Evaluator\EvaluatorExceptionType;
use Elfennol\MonkeyPhp\Evaluator\EvaluatorInterface;
use Elfennol\MonkeyPhp\Node\Catalog\Expr\AssignNode;
use Elfennol\MonkeyPhp\SysObject\Catalog\UnitSysObject;
use Elfennol\MonkeyPhp\SysObject\Context\ContextInterface;
use Elfennol\MonkeyPhp\SysObject\SysObjectInterface;

readonly class AssignEval
{
    public function evaluate(
        EvaluatorInterface $evaluator,
        AssignNode $node,
        ContextInterface $context
    ): SysObjectInterface {
        $identifierName = $node->identifier()->name();

        if ($context->env()->get($identifierName)->isSome()) {
            $context->env()->set($node->identifier()->name(), $evaluator->evaluate($node->expr(), $context));

            return new UnitSysObject();
        }

        throw new EvaluatorException(
            EvaluatorExceptionType::ContextIdentifierUndefined,
            ['node' => $node->debug()],
            'Identifier undefined.'
        );
    }
}
