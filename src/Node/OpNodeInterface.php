<?php

declare(strict_types=1);

namespace Elfennol\MonkeyPhp\Node;

use Elfennol\MonkeyPhp\Token\TokenType;

interface OpNodeInterface extends ExprNodeInterface
{
    public function operator(): TokenType;
}
