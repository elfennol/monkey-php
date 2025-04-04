<?php

declare(strict_types=1);

namespace Elfennol\MonkeyPhp\Vm;

use ArrayAccess;
use Elfennol\MonkeyPhp\SysObject\SysObjectInterface;

/**
 * @implements ArrayAccess<int, SysObjectInterface>
 */
class Globals implements ArrayAccess
{
    private const int SIZE = 65536;

    /**
     * @var SysObjectInterface[]
     */
    private array $elements = [];

    public function offsetExists(mixed $offset): bool
    {
        return isset($this->elements[$offset]);
    }

    public function offsetGet(mixed $offset): SysObjectInterface
    {
        if (!isset($this->elements[$offset])) {
            throw new VmException(
                VmExceptionType::GlobalUnknown,
                ['index' => $offset],
                'Unable to get global variable.',
            );
        }

        return $this->elements[$offset];
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        if (!is_int($offset)) {
            throw new VmException(
                VmExceptionType::GlobalIndexInvalid,
                ['index' => $offset],
                'Global index must be an integer.',
            );
        }

        if (count($this->elements) >= self::SIZE) {
            throw new VmException(
                VmExceptionType::GlobalOverflow,
                ['index' => $offset, 'sysObject' => $value->type()->name, 'count' => count($this->elements)],
                'Unable to set global variable.',
            );
        }

        $this->elements[$offset] = $value;
    }

    public function offsetUnset(mixed $offset): void
    {
        unset($this->elements[$offset]);
    }
}
