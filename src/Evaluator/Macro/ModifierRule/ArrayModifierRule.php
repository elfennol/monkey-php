<?php

declare(strict_types=1);

namespace Elfennol\MonkeyPhp\Evaluator\Macro\ModifierRule;

use Closure;
use Elfennol\MonkeyPhp\Evaluator\EvaluatorException;
use Elfennol\MonkeyPhp\Evaluator\EvaluatorExceptionType;
use Elfennol\MonkeyPhp\Evaluator\Macro\Modifier;
use Elfennol\MonkeyPhp\Evaluator\Macro\ModifierRuleInterface;
use Elfennol\MonkeyPhp\Node\Catalog\Expr\ArrayNode;
use Elfennol\MonkeyPhp\Node\ExprNodeInterface;
use Elfennol\MonkeyPhp\Node\NodeInterface;

readonly class ArrayModifierRule implements ModifierRuleInterface
{
    public function apply(Modifier $modifier, NodeInterface $node, Closure $action): ModifierRuleResult
    {
        if (false === $this->support($node)) {
            return new ModifierRuleResult(false, $node);
        }

        $elements = [];
        foreach ($node->elements() as $element) {
            $nodeModified = $modifier->modify($element, $action);
            if (!$nodeModified instanceof ExprNodeInterface) {
                throw new EvaluatorException(
                    EvaluatorExceptionType::NodeInvalid,
                    ['node' => $node->debug(), 'nodeModified' => $nodeModified->debug()],
                    'Macro: elements must be a list of expression for an array.'
                );
            }

            $elements[] = $nodeModified;
        }

        return new ModifierRuleResult(true, $node->buildWith($elements));
    }

    /**
     * @phpstan-assert-if-true ArrayNode $node
     */
    private function support(NodeInterface $node): bool
    {
        return $node instanceof ArrayNode;
    }
}
