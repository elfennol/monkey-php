<?php

declare(strict_types=1);

namespace Elfennol\MonkeyPhp\Vm;

use Elfennol\MonkeyPhp\SysObject\SysObjectInterface;
use Elfennol\MonkeyPhp\Utils\Option\None;
use Elfennol\MonkeyPhp\Utils\Option\Option;
use Elfennol\MonkeyPhp\Utils\Option\Some;

class Stack
{
    private const int SIZE = 2048;

    /**
     * @var SysObjectInterface[]
     */
    private array $elements;

    private int $pointer;

    /**
     * @var Option<SysObjectInterface>
     */
    private Option $lastElement;

    public function __construct()
    {
        $this->elements = [];
        $this->pointer = 0;
        $this->lastElement = new None();
    }

    public function push(SysObjectInterface $element): void
    {
        if ($this->pointer >= self::SIZE) {
            throw new VmException(
                VmExceptionType::StackOverflow,
                ['pointer' => $this->pointer, 'elementType' => $element->type()->name],
                'Unable to push element on stack.'
            );
        }

        $this->elements[$this->pointer] = $element;
        $this->pointer++;
    }

    public function pop(): SysObjectInterface
    {
        $element = array_pop($this->elements);

        if (null === $element) {
            throw new VmException(
                VmExceptionType::StackUnderflow,
                ['pointer' => $this->pointer],
                'Unable to pop element from stack.'
            );
        }

        $this->lastElement = new Some($element);

        return $element;
    }

    /**
     * @return Option<SysObjectInterface>
     */
    public function last(): Option
    {
        return $this->lastElement;
    }
}
