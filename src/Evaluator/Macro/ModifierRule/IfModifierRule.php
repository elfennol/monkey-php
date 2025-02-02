<?php

declare(strict_types=1);

namespace Elfennol\MonkeyPhp\Evaluator\Macro\ModifierRule;

use Closure;
use Elfennol\MonkeyPhp\Evaluator\EvaluatorException;
use Elfennol\MonkeyPhp\Evaluator\EvaluatorExceptionType;
use Elfennol\MonkeyPhp\Evaluator\Macro\Modifier;
use Elfennol\MonkeyPhp\Evaluator\Macro\ModifierRuleInterface;
use Elfennol\MonkeyPhp\Node\Catalog\BlockNode;
use Elfennol\MonkeyPhp\Node\Catalog\Expr\IfNode;
use Elfennol\MonkeyPhp\Node\ExprNodeInterface;
use Elfennol\MonkeyPhp\Node\NodeInterface;
use Elfennol\MonkeyPhp\Utils\Option\None;
use Elfennol\MonkeyPhp\Utils\Option\Some;

readonly class IfModifierRule implements ModifierRuleInterface
{
    public function apply(Modifier $modifier, NodeInterface $node, Closure $action): ModifierRuleResult
    {
        if (false === $this->support($node)) {
            return new ModifierRuleResult(false, $node);
        }

        $expr = $modifier->modify($node->expr(), $action);
        $consequence = $modifier->modify($node->consequence(), $action);
        $alternative = new None();
        if ($node->alternative()->isSome()) {
            $alternative = new Some($modifier->modify($node->alternative()->unwrap(), $action));
        }

        if (
            !$expr instanceof ExprNodeInterface
            || !$consequence instanceof BlockNode
            || ($alternative->isSome() && !$alternative->unwrap() instanceof BlockNode)
        ) {
            throw new EvaluatorException(
                EvaluatorExceptionType::NodeInvalid,
                [
                    'node' => $node->debug(),
                    'exprNodeModified' => $expr->debug(),
                    'consequenceNodeModified' => $consequence->debug(),
                    'alternativeNodeModified' => $alternative->isSome() ? $alternative->unwrap()->debug() : ''
                ],
                'Macro: condition must be a an expression, consequence and alternative must be a expression for a if.'
            );
        }

        /** @phpstan-ignore argument.type */
        return new ModifierRuleResult(true, $node->buildWith($expr, $consequence, $alternative));
    }

    /**
     * @phpstan-assert-if-true IfNode $node
     */
    private function support(NodeInterface $node): bool
    {
        return $node instanceof IfNode;
    }
}
