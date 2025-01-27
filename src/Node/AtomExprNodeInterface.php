<?php

declare(strict_types=1);

namespace Elfennol\MonkeyPhp\Node;

interface AtomExprNodeInterface extends ExprNodeInterface
{
    /**
     * @return string
     */
    public function value(): string;
}
