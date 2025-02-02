<?php

declare(strict_types=1);

namespace Elfennol\MonkeyPhp\Evaluator\Macro\ModifierRule;

use Closure;
use Elfennol\MonkeyPhp\Evaluator\EvaluatorException;
use Elfennol\MonkeyPhp\Evaluator\EvaluatorExceptionType;
use Elfennol\MonkeyPhp\Evaluator\Macro\Modifier;
use Elfennol\MonkeyPhp\Evaluator\Macro\ModifierRuleInterface;
use Elfennol\MonkeyPhp\Node\Catalog\Expr\IndexExprNode;
use Elfennol\MonkeyPhp\Node\ExprNodeInterface;
use Elfennol\MonkeyPhp\Node\NodeInterface;

readonly class IndexExprModifierRule implements ModifierRuleInterface
{
    public function apply(Modifier $modifier, NodeInterface $node, Closure $action): ModifierRuleResult
    {
        if (false === $this->support($node)) {
            return new ModifierRuleResult(false, $node);
        }

        $left = $modifier->modify($node->left(), $action);
        $index = $modifier->modify($node->index(), $action);

        if (!$left instanceof ExprNodeInterface || !$index instanceof ExprNodeInterface) {
            throw new EvaluatorException(
                EvaluatorExceptionType::NodeInvalid,
                [
                    'node' => $node->debug(),
                    'leftNodeModified' => $left->debug(),
                    'indexNodeModified' => $index->debug()
                ],
                'Macro: left and index must be a expression.'
            );
        }

        return new ModifierRuleResult(true, $node->buildWith($left, $index));
    }

    /**
     * @phpstan-assert-if-true IndexExprNode $node
     */
    private function support(NodeInterface $node): bool
    {
        return $node instanceof IndexExprNode;
    }
}
