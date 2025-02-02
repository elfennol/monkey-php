<?php

declare(strict_types=1);

namespace Elfennol\MonkeyPhp\Evaluator\Macro;

use Closure;
use Elfennol\MonkeyPhp\Evaluator\EvaluatorException;
use Elfennol\MonkeyPhp\Evaluator\EvaluatorExceptionType;
use Elfennol\MonkeyPhp\Evaluator\ModifierInterface;
use Elfennol\MonkeyPhp\Node\NodeInterface;

readonly class Modifier implements ModifierInterface
{
    /**
     * @param ModifierRuleInterface[] $rules
     */
    public function __construct(private array $rules)
    {
    }

    public function modify(NodeInterface $node, Closure $action): NodeInterface
    {
        foreach ($this->rules as $rule) {
            $result = $rule->apply($this, $node, $action);
            if (true === $result->applied) {
                return $result->node;
            }
        }

        throw new EvaluatorException(
            EvaluatorExceptionType::NodeInvalid,
            ['node' => $node->debug()],
            'Macro: unable to modify node.'
        );
    }
}
