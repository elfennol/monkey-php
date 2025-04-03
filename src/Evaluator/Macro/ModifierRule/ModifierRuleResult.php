<?php

declare(strict_types=1);

namespace Elfennol\MonkeyPhp\Evaluator\Macro\ModifierRule;

use Elfennol\MonkeyPhp\Node\NodeInterface;

readonly class ModifierRuleResult
{
    public function __construct(public bool $applied, public NodeInterface $node)
    {
    }
}
