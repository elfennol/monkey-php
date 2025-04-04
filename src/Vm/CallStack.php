<?php

declare(strict_types=1);

namespace Elfennol\MonkeyPhp\Vm;

class CallStack
{
    public const int MAX_DEPTH = 1024;

    /** @var Frame[] */
    private array $frames = [];

    public function push(Frame $frame): void
    {
        if (count($this->frames) >= self::MAX_DEPTH) {
            throw new VmException(VmExceptionType::CallStackOverflow, []);
        }

        $this->frames[] = $frame;
    }

    public function pop(): Frame
    {
        if (empty($this->frames)) {
            throw new VmException(VmExceptionType::CallStackUnderflow, []);
        }

        return array_pop($this->frames);
    }

    public function current(): Frame
    {
        if (empty($this->frames)) {
            throw new VmException(VmExceptionType::CallStackUnderflow, []);
        }

        return array_last($this->frames);
    }
}
