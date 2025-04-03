<?php

declare(strict_types=1);

namespace Elfennol\MonkeyPhp\Evaluator\Macro\ModifierRule;

use Closure;
use Elfennol\MonkeyPhp\Evaluator\EvaluatorException;
use Elfennol\MonkeyPhp\Evaluator\EvaluatorExceptionType;
use Elfennol\MonkeyPhp\Evaluator\Macro\Modifier;
use Elfennol\MonkeyPhp\Evaluator\Macro\ModifierRuleInterface;
use Elfennol\MonkeyPhp\Node\Catalog\Expr\PostfixOpNode;
use Elfennol\MonkeyPhp\Node\ExprNodeInterface;
use Elfennol\MonkeyPhp\Node\NodeInterface;

readonly class PostfixModifierRule implements ModifierRuleInterface
{
    public function apply(Modifier $modifier, NodeInterface $node, Closure $action): ModifierRuleResult
    {
        if (false === $this->support($node)) {
            return new ModifierRuleResult(false, $node);
        }

        $operand = $modifier->modify($node->operand(), $action);

        if (!$operand instanceof ExprNodeInterface) {
            throw new EvaluatorException(
                EvaluatorExceptionType::NodeInvalid,
                ['node' => $node->debug(), 'operandNodeModified' => $operand->debug()],
                'Macro: operand must be a expression for a postfix expression.'
            );
        }

        return new ModifierRuleResult(true, $node->buildWith($operand));
    }

    /**
     * @phpstan-assert-if-true PostfixOpNode $node
     */
    private function support(NodeInterface $node): bool
    {
        return $node instanceof PostfixOpNode;
    }
}
