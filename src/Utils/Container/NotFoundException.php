<?php

declare(strict_types=1);

namespace Elfennol\MonkeyPhp\Utils\Container;

use Exception;
use Psr\Container\NotFoundExceptionInterface;

class NotFoundException extends Exception implements NotFoundExceptionInterface
{
}
