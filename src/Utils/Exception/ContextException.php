<?php

declare(strict_types=1);

namespace Elfennol\MonkeyPhp\Utils\Exception;

use Exception;
use UnitEnum;

/**
 * @template T of UnitEnum
 */
abstract class ContextException extends Exception
{
    /**
     * @param T $type
     * @param array<string, mixed>|array{} $context
     */
    public function __construct(
        private readonly UnitEnum $type,
        private readonly array $context = [],
        string $message = '',
    ) {
        parent::__construct($message);
    }

    /**
     * @return T
     */
    public function getType(): UnitEnum
    {
        return $this->type;
    }

    /**
     * @return array<string, mixed>|array{}
     */
    public function getContext(): array
    {
        return $this->context;
    }
}
