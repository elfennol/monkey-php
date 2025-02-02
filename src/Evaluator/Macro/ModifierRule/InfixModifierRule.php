<?php

declare(strict_types=1);

namespace Elfennol\MonkeyPhp\Evaluator\Macro\ModifierRule;

use Closure;
use Elfennol\MonkeyPhp\Evaluator\EvaluatorException;
use Elfennol\MonkeyPhp\Evaluator\EvaluatorExceptionType;
use Elfennol\MonkeyPhp\Evaluator\Macro\Modifier;
use Elfennol\MonkeyPhp\Evaluator\Macro\ModifierRuleInterface;
use Elfennol\MonkeyPhp\Node\Catalog\Expr\InfixOpNode;
use Elfennol\MonkeyPhp\Node\ExprNodeInterface;
use Elfennol\MonkeyPhp\Node\NodeInterface;

readonly class InfixModifierRule implements ModifierRuleInterface
{
    public function apply(Modifier $modifier, NodeInterface $node, Closure $action): ModifierRuleResult
    {
        if (false === $this->support($node)) {
            return new ModifierRuleResult(false, $node);
        }

        $left = $modifier->modify($node->leftOperand(), $action);
        $right = $modifier->modify($node->rightOperand(), $action);

        if (!$left instanceof ExprNodeInterface || !$right instanceof ExprNodeInterface) {
            throw new EvaluatorException(
                EvaluatorExceptionType::NodeInvalid,
                [
                    'node' => $node->debug(),
                    'leftNodeModified' => $left->debug(),
                    'rightNodeModified' => $right->debug()
                ],
                'Macro: left and right must be a expression for an infix expression.'
            );
        }

        return new ModifierRuleResult(true, $node->buildWith($left, $right));
    }

    /**
     * @phpstan-assert-if-true InfixOpNode $node
     */
    private function support(NodeInterface $node): bool
    {
        return $node instanceof InfixOpNode;
    }
}
