<?php

declare(strict_types=1);

namespace Elfennol\MonkeyPhp\Evaluator\SubEval;

use Elfennol\MonkeyPhp\Evaluator\EvaluatorException;
use Elfennol\MonkeyPhp\Evaluator\EvaluatorExceptionType;
use Elfennol\MonkeyPhp\Evaluator\EvaluatorInterface;
use Elfennol\MonkeyPhp\Node\Catalog\Stmt\LetNode;
use Elfennol\MonkeyPhp\SysObject\Catalog\UnitSysObject;
use Elfennol\MonkeyPhp\SysObject\Context\ContextInterface;
use Elfennol\MonkeyPhp\SysObject\SysObjectInterface;

readonly class LetEval
{
    public function evaluate(
        EvaluatorInterface $evaluator,
        LetNode $node,
        ContextInterface $context
    ): SysObjectInterface {
        $identifierName = $node->identifier()->name();
        if (true === $context->exists($identifierName)) {
            throw new EvaluatorException(
                EvaluatorExceptionType::ContextIdentifierConflict,
                ['node' => $node->debug()],
                'Identifier already defined.'
            );
        }

        $context->env()->set($node->identifier()->name(), $evaluator->evaluate($node->expr(), $context));

        return new UnitSysObject();
    }
}
