<?php

declare(strict_types=1);

namespace Elfennol\MonkeyPhp\Node;

use JsonSerializable;

interface NodeInterface extends JsonSerializable
{
    public function type(): NodeType;

    /**
     * Used only in exception and log. Array keys must not be used.
     *
     * @return mixed[]
     */
    public function debug(): array;
}
