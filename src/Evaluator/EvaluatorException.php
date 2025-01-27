<?php

declare(strict_types=1);

namespace Elfennol\MonkeyPhp\Evaluator;

use Exception;

class EvaluatorException extends Exception
{
    /**
     * @param array<string, mixed>|array{} $context
     */
    public function __construct(
        private readonly EvaluatorExceptionType $type,
        private readonly array $context = [],
        string $message = '',
    ) {
        parent::__construct($message);
    }

    public function getType(): EvaluatorExceptionType
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
