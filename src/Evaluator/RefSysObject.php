<?php

declare(strict_types=1);

namespace Elfennol\MonkeyPhp\Evaluator;

use Elfennol\MonkeyPhp\SysObject\Catalog\BoolSysObject;

readonly class RefSysObject
{
    public BoolSysObject $true;
    public BoolSysObject $false;

    public function __construct()
    {
        $this->true = new BoolSysObject('true');
        $this->false = new BoolSysObject('false');
    }
}
