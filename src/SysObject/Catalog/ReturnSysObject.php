<?php

declare(strict_types=1);

namespace Elfennol\MonkeyPhp\SysObject\Catalog;

use Elfennol\MonkeyPhp\SysObject\SysObjectInterface;
use Elfennol\MonkeyPhp\SysObject\SysObjectType;
use Elfennol\MonkeyPhp\SysObject\TerminusSysObjectInterface;

readonly class ReturnSysObject implements SysObjectInterface, TerminusSysObjectInterface
{
    public function __construct(private SysObjectInterface $value)
    {
    }

    public function type(): SysObjectType
    {
        return SysObjectType::Return;
    }

    public function value(): SysObjectInterface
    {
        return $this->value;
    }
}
