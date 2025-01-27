<?php

declare(strict_types=1);

namespace Elfennol\MonkeyPhp\Parser;

use Exception;

class ParserException extends Exception
{
    /**
     * @param array<string, mixed>|array{} $context
     */
    public function __construct(
        private readonly ParserExceptionType $type,
        private readonly array $context = [],
        string $message = '',
    ) {
        parent::__construct($message);
    }

    public function getType(): ParserExceptionType
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
