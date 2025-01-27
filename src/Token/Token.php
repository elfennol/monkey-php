<?php

declare(strict_types=1);

namespace Elfennol\MonkeyPhp\Token;

use Elfennol\MonkeyPhp\Token\Debug\Debug;
use JsonSerializable;

readonly class Token implements JsonSerializable
{
    public Debug $debug;

    public function __construct(
        public TokenType $type,
        public string $value,
        private int $line,
        private int $col,
    ) {
        $this->debug = new Debug($this->line, $this->col);
    }

    /**
     * @return array{type: TokenType, value: string, debug: Debug}
     */
    public function jsonSerialize(): array
    {
        return $this->normalize();
    }

    /**
     * @return array{type: TokenType, value: string, debug: Debug}
     */
    public function normalize(): array
    {
        return [
            'type' => $this->type,
            'value' => $this->value,
            'debug' => $this->debug,
        ];
    }
}
