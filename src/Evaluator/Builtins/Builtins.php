<?php

declare(strict_types=1);

namespace Elfennol\MonkeyPhp\Evaluator\Builtins;

use Elfennol\MonkeyPhp\SysObject\Catalog\BuiltinSysObject;
use Elfennol\MonkeyPhp\Utils\Option\None;
use Elfennol\MonkeyPhp\Utils\Option\Option;
use Elfennol\MonkeyPhp\Utils\Option\Some;

readonly class Builtins
{
    /**
     * @param array<string, BuiltinSysObject> $builtinFns
     */
    public function __construct(private array $builtinFns)
    {
    }

    /**
     * @return Option<BuiltinSysObject>
     */
    public function get(string $name): Option
    {
        if (!isset($this->builtinFns[$name])) {
            return new None();
        }

        return new Some($this->builtinFns[$name]);
    }
}
