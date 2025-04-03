<?php

declare(strict_types=1);

namespace Elfennol\MonkeyPhp\Evaluator\Macro\ModifierRule;

use Closure;
use Elfennol\MonkeyPhp\Evaluator\EvaluatorException;
use Elfennol\MonkeyPhp\Evaluator\EvaluatorExceptionType;
use Elfennol\MonkeyPhp\Evaluator\Macro\Modifier;
use Elfennol\MonkeyPhp\Evaluator\Macro\ModifierRuleInterface;
use Elfennol\MonkeyPhp\Node\Catalog\Stmt\ReturnNode;
use Elfennol\MonkeyPhp\Node\ExprNodeInterface;
use Elfennol\MonkeyPhp\Node\NodeInterface;
use Elfennol\MonkeyPhp\Utils\Option\None;
use Elfennol\MonkeyPhp\Utils\Option\Some;

readonly class ReturnModifierRule implements ModifierRuleInterface
{
    public function apply(Modifier $modifier, NodeInterface $node, Closure $action): ModifierRuleResult
    {
        if (false === $this->support($node)) {
            return new ModifierRuleResult(false, $node);
        }

        if ($node->expr()->isNone()) {
            return new ModifierRuleResult(true, $node->buildWith(new None()));
        }

        $nodeModified = $modifier->modify($node->expr()->unwrap(), $action);

        if (!$nodeModified instanceof ExprNodeInterface) {
            throw new EvaluatorException(
                EvaluatorExceptionType::NodeInvalid,
                [
                    'node' => $node->debug(),
                    'nodeModified' => $nodeModified->debug(),
                ],
                'Macro: node must be a expression or empty for a return.'
            );
        }

        return new ModifierRuleResult(true, $node->buildWith(new Some($nodeModified)));
    }

    /**
     * @phpstan-assert-if-true ReturnNode $node
     */
    private function support(NodeInterface $node): bool
    {
        return $node instanceof ReturnNode;
    }
}
