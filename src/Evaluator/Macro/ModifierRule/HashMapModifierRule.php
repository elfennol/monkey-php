<?php

declare(strict_types=1);

namespace Elfennol\MonkeyPhp\Evaluator\Macro\ModifierRule;

use Closure;
use Elfennol\MonkeyPhp\Evaluator\EvaluatorException;
use Elfennol\MonkeyPhp\Evaluator\EvaluatorExceptionType;
use Elfennol\MonkeyPhp\Evaluator\Macro\Modifier;
use Elfennol\MonkeyPhp\Evaluator\Macro\ModifierRuleInterface;
use Elfennol\MonkeyPhp\Node\Catalog\Expr\HashMapNode;
use Elfennol\MonkeyPhp\Node\ExprNodeInterface;
use Elfennol\MonkeyPhp\Node\NodeInterface;

readonly class HashMapModifierRule implements ModifierRuleInterface
{
    public function apply(Modifier $modifier, NodeInterface $node, Closure $action): ModifierRuleResult
    {
        if (false === $this->support($node)) {
            return new ModifierRuleResult(false, $node);
        }

        $elementsModified = [];
        foreach ($node->elements() as $element) {
            $keyModified = $modifier->modify($element->key(), $action);
            $valueModified = $modifier->modify($element->value(), $action);

            if (!$keyModified instanceof ExprNodeInterface || !$valueModified instanceof ExprNodeInterface) {
                throw new EvaluatorException(
                    EvaluatorExceptionType::NodeInvalid,
                    [
                        'node' => $node->debug(),
                        'keyModified' => $keyModified->debug(),
                        'valueModified' => $valueModified->debug()
                    ],
                    'Macro: key and value must be a expression for a hash map.'
                );
            }

            $elementsModified[] = $element->buildWith($keyModified, $valueModified);
        }

        return new ModifierRuleResult(true, $node->buildWith($elementsModified));
    }

    /**
     * @phpstan-assert-if-true HashMapNode $node
     */
    private function support(NodeInterface $node): bool
    {
        return $node instanceof HashMapNode;
    }
}
