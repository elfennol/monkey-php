<?php

declare(strict_types=1);

namespace Elfennol\MonkeyPhp\Evaluator;

use Closure;
use Elfennol\MonkeyPhp\Node\NodeInterface;

interface ModifierInterface
{
    /**
     * @param Closure(NodeInterface): NodeInterface $action
     */
    public function modify(NodeInterface $node, Closure $action): NodeInterface;
}
