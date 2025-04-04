<?php

declare(strict_types=1);

namespace Elfennol\MonkeyPhp\Utils\Type;

trait HashableTrait
{
    public function hash(): string
    {
        return hash('sha256', sprintf('%s::%s', $this::class, implode(':', $this->hashableProperties())));
    }

    /**
     * @return string[]
     */
    abstract protected function hashableProperties(): array;
}
