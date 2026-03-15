<?php

declare(strict_types=1);

namespace Elfennol\MonkeyPhp\SysObject\Catalog;

use Elfennol\MonkeyPhp\SysObject\ConstantTypeInterface;
use Elfennol\MonkeyPhp\SysObject\SysObjectType;
use Elfennol\MonkeyPhp\Utils\Type\Byte;
use Elfennol\MonkeyPhp\Utils\Type\HashableTrait;

readonly class CompiledFnSysObject implements ConstantTypeInterface
{
    use HashableTrait;

    /**
     * @param Byte[] $instructions
     */
    public function __construct(
        private array $instructions,
    ) {
    }

    public function type(): SysObjectType
    {
        return SysObjectType::CompiledFunction;
    }

    /**
     * @return Byte[]
     */
    public function instructions(): array
    {
        return $this->instructions;
    }

    public function __toString(): string
    {
        return 'fn';
    }

    protected function hashableProperties(): array
    {
        return [serialize($this->instructions)];
    }
}
