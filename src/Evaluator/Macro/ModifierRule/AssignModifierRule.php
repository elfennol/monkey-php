<?php

declare(strict_types=1);

namespace Elfennol\MonkeyPhp\Evaluator\Macro\ModifierRule;

use Closure;
use Elfennol\MonkeyPhp\Evaluator\EvaluatorException;
use Elfennol\MonkeyPhp\Evaluator\EvaluatorExceptionType;
use Elfennol\MonkeyPhp\Evaluator\Macro\Modifier;
use Elfennol\MonkeyPhp\Evaluator\Macro\ModifierRuleInterface;
use Elfennol\MonkeyPhp\Node\Catalog\Expr\AssignNode;
use Elfennol\MonkeyPhp\Node\Catalog\Expr\IdentifierNode;
use Elfennol\MonkeyPhp\Node\ExprNodeInterface;
use Elfennol\MonkeyPhp\Node\NodeInterface;

readonly class AssignModifierRule implements ModifierRuleInterface
{
    public function apply(Modifier $modifier, NodeInterface $node, Closure $action): ModifierRuleResult
    {
        if (false === $this->support($node)) {
            return new ModifierRuleResult(false, $node);
        }

        $identNodeModified = $modifier->modify($node->identifier(), $action);
        $exprNodeModified = $modifier->modify($node->expr(), $action);

        if (!$identNodeModified instanceof IdentifierNode || !$exprNodeModified instanceof ExprNodeInterface) {
            throw new EvaluatorException(
                EvaluatorExceptionType::NodeInvalid,
                [
                    'node' => $node->debug(),
                    'identifierNodeModified' => $identNodeModified->debug(),
                    'exprNodeModified' => $exprNodeModified->debug(),
                ],
                'Macro: value must be a expression for a let.'
            );
        }

        return new ModifierRuleResult(true, $node->buildWith($identNodeModified, $exprNodeModified));
    }

    /**
     * @phpstan-assert-if-true AssignNode $node
     */
    private function support(NodeInterface $node): bool
    {
        return $node instanceof AssignNode;
    }
}
