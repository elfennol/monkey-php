<?php

declare(strict_types=1);

namespace Elfennol\MonkeyPhp\Lexer;

use Exception;

class LexerException extends Exception
{
    /**
     * @param array<string, mixed>|array{} $context
     */
    public function __construct(
        private readonly LexerExceptionType $type,
        private readonly array $context = [],
        string $message = '',
    ) {
        parent::__construct($message);
    }

    public function getType(): LexerExceptionType
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
