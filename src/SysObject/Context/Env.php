<?php

declare(strict_types=1);

namespace Elfennol\MonkeyPhp\SysObject\Context;

use Elfennol\MonkeyPhp\SysObject\SysObjectInterface;
use Elfennol\MonkeyPhp\Utils\Option\None;
use Elfennol\MonkeyPhp\Utils\Option\Option;
use Elfennol\MonkeyPhp\Utils\Option\Some;

class Env implements EnvInterface
{
    /**
     * @var array<string, SysObjectInterface>
     */
    private array $store = [];

    /**
     * @param Option<EnvInterface> $outer
     */
    public function __construct(
        private readonly Option $outer = new None(),
    ) {
    }

    /**
     * @return Option<SysObjectInterface>
     */
    public function get(string $name): Option
    {
        if (isset($this->store[$name])) {
            return new Some($this->store[$name]);
        }

        if ($this->outer->isSome()) {
            return $this->outer->unwrap()->get($name);
        }

        return new None();
    }

    public function set(string $name, SysObjectInterface $sysObject): void
    {
        $this->store[$name] = $sysObject;
    }
}
