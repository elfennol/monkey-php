<?php

declare(strict_types=1);

namespace Elfennol\MonkeyPhp\Evaluator\Macro\ModifierRule;

use Closure;
use Elfennol\MonkeyPhp\Evaluator\EvaluatorException;
use Elfennol\MonkeyPhp\Evaluator\EvaluatorExceptionType;
use Elfennol\MonkeyPhp\Evaluator\Macro\Modifier;
use Elfennol\MonkeyPhp\Evaluator\Macro\ModifierRuleInterface;
use Elfennol\MonkeyPhp\Node\Catalog\BlockNode;
use Elfennol\MonkeyPhp\Node\Catalog\Expr\FnNode;
use Elfennol\MonkeyPhp\Node\Catalog\Expr\IdentifierNode;
use Elfennol\MonkeyPhp\Node\NodeInterface;

readonly class FnModifierRule implements ModifierRuleInterface
{
    public function apply(Modifier $modifier, NodeInterface $node, Closure $action): ModifierRuleResult
    {
        if (false === $this->support($node)) {
            return new ModifierRuleResult(false, $node);
        }

        $identifiersModified = [];
        foreach ($node->fnParams()->identifiers() as $identifier) {
            $identifierModified = $modifier->modify($identifier, $action);
            if (!$identifierModified instanceof IdentifierNode) {
                throw new EvaluatorException(
                    EvaluatorExceptionType::NodeInvalid,
                    ['node' => $node->debug(), 'identifierModified' => $identifierModified->debug()],
                    'Macro: fn params must be a list of identifiers.'
                );
            }

            $identifiersModified[] = $identifierModified;
        }

        $bodyModified = $modifier->modify($node->body(), $action);
        if (!$bodyModified instanceof BlockNode) {
            throw new EvaluatorException(
                EvaluatorExceptionType::NodeInvalid,
                ['node' => $node->debug(), 'bodyModified' => $bodyModified->debug()],
                'Macro: fn body must be a block.'
            );
        }

        return new ModifierRuleResult(
            true,
            $node->buildWith($node->fnParams()->buildWith($identifiersModified), $bodyModified)
        );
    }

    /**
     * @phpstan-assert-if-true FnNode $node
     */
    private function support(NodeInterface $node): bool
    {
        return $node instanceof FnNode;
    }
}
