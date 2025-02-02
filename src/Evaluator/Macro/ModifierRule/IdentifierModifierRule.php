<?php

declare(strict_types=1);

namespace Elfennol\MonkeyPhp\Evaluator\Macro\ModifierRule;

use Closure;
use Elfennol\MonkeyPhp\Evaluator\Macro\Modifier;
use Elfennol\MonkeyPhp\Evaluator\Macro\ModifierRuleInterface;
use Elfennol\MonkeyPhp\Node\Catalog\Expr\IdentifierNode;
use Elfennol\MonkeyPhp\Node\NodeInterface;

readonly class IdentifierModifierRule implements ModifierRuleInterface
{
    /**
     * {@inheritdoc}
     */
    public function apply(Modifier $modifier, NodeInterface $node, Closure $action): ModifierRuleResult
    {
        if (false === $this->support($node)) {
            return new ModifierRuleResult(false, $node);
        }

        return new ModifierRuleResult(true, $action($node));
    }

    /**
     * @phpstan-assert-if-true IdentifierNode $node
     */
    private function support(NodeInterface $node): bool
    {
        return $node instanceof IdentifierNode;
    }
}
