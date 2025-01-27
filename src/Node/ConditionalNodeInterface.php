<?php

declare(strict_types=1);

namespace Elfennol\MonkeyPhp\Node;

use Elfennol\MonkeyPhp\Node\Catalog\BlockNode;
use Elfennol\MonkeyPhp\Utils\Option\Option;

interface ConditionalNodeInterface extends ExprNodeInterface
{
    public function expr(): ExprNodeInterface;

    public function consequence(): BlockNode;

    /**
     * @return Option<BlockNode>
     */
    public function alternative(): Option;
}
