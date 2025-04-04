<?php

declare(strict_types=1);

namespace Elfennol\MonkeyPhp\Compiler;

use Elfennol\MonkeyPhp\Node\NodeInterface;

interface CompilerRuleInterface
{
    public function apply(CompilerContext $context, NodeInterface $node): bool;
}
