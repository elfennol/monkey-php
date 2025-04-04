<?php

declare(strict_types=1);

namespace Elfennol\MonkeyPhp\Utils\Exception;

use Throwable;
use UnitEnum;

interface ContextExceptionInterface extends Throwable
{
    public function getType(): UnitEnum;

    /**
     * @return array<string, mixed>|array{}
     */
    public function getContext(): array;
}
