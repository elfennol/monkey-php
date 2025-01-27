<?php

declare(strict_types=1);

namespace Elfennol\MonkeyPhp\Evaluator\SubEval;

use Elfennol\MonkeyPhp\Evaluator\ContextInterface;
use Elfennol\MonkeyPhp\Evaluator\EvaluatorException;
use Elfennol\MonkeyPhp\Evaluator\EvaluatorExceptionType;
use Elfennol\MonkeyPhp\Node\Catalog\Expr\IdentifierNode;
use Elfennol\MonkeyPhp\SysObject\SysObjectInterface;

readonly class IdentifierEval
{
    public function evaluate(IdentifierNode $node, ContextInterface $context): SysObjectInterface
    {
        $envObject = $context->env()->get($node->name());
        $builtin = $context->builtins()->get($node->name());

        if ($envObject->isSome()) {
            return $envObject->unwrap();
        }

        if ($builtin->isSome()) {
            return $builtin->unwrap();
        }

        throw new EvaluatorException(
            EvaluatorExceptionType::ContextIdentifierNotFound,
            ['node' => $node->debug()],
            'Unable to evaluate identifier node.'
        );
    }
}
