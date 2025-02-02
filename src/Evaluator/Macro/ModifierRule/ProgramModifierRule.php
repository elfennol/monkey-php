<?php

declare(strict_types=1);

namespace Elfennol\MonkeyPhp\Evaluator\Macro\ModifierRule;

use Closure;
use Elfennol\MonkeyPhp\Evaluator\EvaluatorException;
use Elfennol\MonkeyPhp\Evaluator\EvaluatorExceptionType;
use Elfennol\MonkeyPhp\Evaluator\Macro\Modifier;
use Elfennol\MonkeyPhp\Evaluator\Macro\ModifierRuleInterface;
use Elfennol\MonkeyPhp\Node\Catalog\ProgramNode;
use Elfennol\MonkeyPhp\Node\NodeInterface;
use Elfennol\MonkeyPhp\Node\StmtNodeInterface;

readonly class ProgramModifierRule implements ModifierRuleInterface
{
    public function apply(Modifier $modifier, NodeInterface $node, Closure $action): ModifierRuleResult
    {
        if (false === $this->support($node)) {
            return new ModifierRuleResult(false, $node);
        }

        $stmts = [];
        foreach ($node->stmts() as $stmt) {
            $nodeModified = $modifier->modify($stmt, $action);
            if (!$nodeModified instanceof StmtNodeInterface) {
                throw new EvaluatorException(
                    EvaluatorExceptionType::NodeInvalid,
                    ['node' => $node->debug(), 'nodeModified' => $nodeModified->debug()],
                    'Macro: nodes must be a list of statements for a program.'
                );
            }

            $stmts[] = $nodeModified;
        }

        return new ModifierRuleResult(true, $node->buildWith($stmts));
    }

    /**
     * @phpstan-assert-if-true ProgramNode $node
     */
    private function support(NodeInterface $node): bool
    {
        return $node instanceof ProgramNode;
    }
}
