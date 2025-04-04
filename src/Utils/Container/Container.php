<?php

declare(strict_types=1);

namespace Elfennol\MonkeyPhp\Utils\Container;

use Closure;
use Psr\Container\ContainerInterface;
use ReflectionClass;
use ReflectionException;
use ReflectionNamedType;
use ReflectionParameter;

/**
 * PSR-11 compliant dependency injection container with autowiring support.
 */
class Container implements ContainerInterface
{
    /**
     * @var array<class-string, object>
     */
    private array $instances = [];

    /**
     * @param array<class-string, Closure(static): object> $definitions
     * @param array<class-string, class-string> $interfaces
     */
    public function __construct(
        private readonly array $definitions = [],
        private array $interfaces = [],
    ) {
    }

    /**
     * @SuppressWarnings(PHPMD.ShortVariable)
     *
     * @template T of object
     *
     * @param class-string<T> $id
     *
     * @return T
     */
    public function get(string $id): object
    {
        if (isset($this->instances[$id])) {
            /** @var T */
            return $this->instances[$id];
        }

        if (isset($this->definitions[$id])) {
            $this->instances[$id] = ($this->definitions[$id])($this);
            /** @var T */
            return $this->instances[$id];
        }

        if (isset($this->interfaces[$id])) {
            $this->instances[$id] = $this->get($this->interfaces[$id]);
            /** @var T */
            return $this->instances[$id];
        }

        $this->instances[$id] = $this->resolve($id);

        return $this->instances[$id];
    }

    /**
     * @SuppressWarnings(PHPMD.ShortVariable)
     */
    public function has(string $id): bool
    {
        return isset($this->definitions[$id]) || isset($this->interfaces[$id]);
    }

    /**
     * @param class-string $interface
     * @param class-string $implementation
     */
    public function addInterface(string $interface, string $implementation): void
    {
        $this->interfaces[$interface] = $implementation;
    }

    /**
     * @SuppressWarnings(PHPMD.ShortVariable)
     *
     * @template T of object
     *
     * @param class-string<T> $id
     *
     * @return T
     */
    private function resolve(string $id): object
    {
        if (!class_exists($id)) {
            throw new ContainerException(sprintf('%s is not a class name', $id));
        }

        $reflection = new ReflectionClass($id);
        $constructor = $reflection->getConstructor();

        if (null === $constructor || 0 === $constructor->getNumberOfParameters()) {
            return new $id();
        }

        try {
            $dependencies = $this->resolveConstructorDependencies($constructor->getParameters());

            return $reflection->newInstanceArgs($dependencies);
        } catch (ReflectionException $e) {
            throw new ContainerException(
                sprintf('Cannot instantiate service %s: %s', $id, $e->getMessage()),
                previous: $e
            );
        }
    }

    /**
     * @param ReflectionParameter[] $parameters
     *
     * @return array<int, mixed>
     */
    private function resolveConstructorDependencies(array $parameters): array
    {
        $dependencies = [];

        foreach ($parameters as $parameter) {
            $type = $parameter->getType();

            if ($type instanceof ReflectionNamedType && !$type->isBuiltin()) {
                /** @var class-string $typeName */
                $typeName = $type->getName();
                $dependencies[] = $this->get($this->interfaces[$typeName] ?? $typeName);
                continue;
            }

            throw new ContainerException(
                sprintf(
                    'Cannot resolve parameter %s for class %s',
                    $parameter->getName(),
                    $parameter->getDeclaringClass()?->getName()
                )
            );
        }

        return $dependencies;
    }
}
