<?php

declare(strict_types=1);

namespace Elfennol\MonkeyPhp\Evaluator\Macro\ModifierRule;

use Closure;
use Elfennol\MonkeyPhp\Evaluator\EvaluatorException;
use Elfennol\MonkeyPhp\Evaluator\EvaluatorExceptionType;
use Elfennol\MonkeyPhp\Evaluator\Macro\Modifier;
use Elfennol\MonkeyPhp\Evaluator\Macro\ModifierRuleInterface;
use Elfennol\MonkeyPhp\Node\CallableExprInterface;
use Elfennol\MonkeyPhp\Node\Catalog\Expr\FnCallNode;
use Elfennol\MonkeyPhp\Node\ExprNodeInterface;
use Elfennol\MonkeyPhp\Node\NodeInterface;

readonly class FnCallModifierRule implements ModifierRuleInterface
{
    public function apply(Modifier $modifier, NodeInterface $node, Closure $action): ModifierRuleResult
    {
        if (false === $this->support($node)) {
            return new ModifierRuleResult(false, $node);
        }

        $fnExprNodeModified = $modifier->modify($node->fnExpr(), $action);
        if (!$fnExprNodeModified instanceof CallableExprInterface) {
            throw new EvaluatorException(
                EvaluatorExceptionType::NodeInvalid,
                [
                    'node' => $node->debug(),
                    'fnExprNodeModified' => $fnExprNodeModified->debug(),
                ],
                'Macro: fn expr must be a callable expression.'
            );
        }

        $fnArgsNodeModified = [];
        foreach ($node->fnArgs() as $fnArg) {
            $fnArgNodeModified = $modifier->modify($fnArg, $action);

            if (!$fnArgNodeModified instanceof ExprNodeInterface) {
                throw new EvaluatorException(
                    EvaluatorExceptionType::NodeInvalid,
                    [
                        'node' => $node->debug(),
                        'fnArgNodeModified' => $fnArgNodeModified->debug(),
                    ],
                    'Macro: fn argument must be a expression.'
                );
            }

            $fnArgsNodeModified[] = $fnArgNodeModified;
        }

        return new ModifierRuleResult(true, $action($node->buildWith($fnExprNodeModified, $fnArgsNodeModified)));
    }

    /**
     * @phpstan-assert-if-true FnCallNode $node
     */
    private function support(NodeInterface $node): bool
    {
        return $node instanceof FnCallNode;
    }
}
