<?php

declare(strict_types=1);

namespace Elfennol\MonkeyPhp\Evaluator\Macro;

use Closure;
use Elfennol\MonkeyPhp\Evaluator\Macro\ModifierRule\ModifierRuleResult;
use Elfennol\MonkeyPhp\Node\NodeInterface;

interface ModifierRuleInterface
{
    /**
     * @param Closure(NodeInterface): NodeInterface $action $modifier
     */
    public function apply(Modifier $modifier, NodeInterface $node, Closure $action): ModifierRuleResult;
}
