<?php

namespace Elfennol\MonkeyPhp\Tests\Utils\Container;

use Elfennol\MonkeyPhp\Utils\Container\Container;
use Elfennol\MonkeyPhp\Utils\Container\ContainerException;
use PHPUnit\Framework\TestCase;
use stdClass;

class ContainerTest extends TestCase
{
    /**
     * Tests that the get method retrieves existing instances from the container.
     */
    public function testGetExistingInstance(): void
    {
        $service = new stdClass();
        $container = new Container(
            definitions: [
                stdClass::class => static fn (Container $container) => $service,
            ]
        );

        $result = $container->get(stdClass::class);

        $this->assertSame($service, $result);
    }

    /**
     * Tests that the get method resolves a class automatically if not explicitly defined.
     */
    public function testAutoResolveClass(): void
    {
        $container = new Container();

        $result = $container->get(stdClass::class);

        $this->assertInstanceOf(stdClass::class, $result);
    }

    /**
     * Tests that the get method resolves a class with constructor dependencies.
     */
    public function testResolveWithConstructorDependencies(): void
    {
        $dependency = new stdClass();
        $container = new Container(
            definitions: [
                stdClass::class => static fn (Container $container) => $dependency,
            ]
        );

        $result = $container->get(ClassWithDependency::class);

        $this->assertInstanceOf(ClassWithDependency::class, $result);
        $this->assertSame($dependency, $result->dependency);
    }

    /**
     * Tests that the get method correctly throws ContainerException for non-existent classes.
     */
    public function testGetNonExistentClassThrowsException(): void
    {
        $this->expectException(ContainerException::class);
        $this->expectExceptionMessage('is not a class name');

        $container = new Container();
        /** @phpstan-ignore argument.type */
        $container->get('NonExistentClass');
    }

    /**
     * Tests that the get method resolves interfaces to their implementations.
     */
    public function testResolveInterfaceToImplementation(): void
    {
        $container = new Container(
            interfaces: [
                ExampleInterface::class => ExampleImplementation::class,
            ]
        );

        $result = $container->get(ExampleInterface::class);

        $this->assertInstanceOf(ExampleImplementation::class, $result);
    }

    /**
     * Tests that the get method reuses existing instances from the container.
     */
    public function testGetReusesInstances(): void
    {
        $container = new Container();

        $instance1 = $container->get(stdClass::class);
        $instance2 = $container->get(stdClass::class);

        $this->assertSame($instance1, $instance2);
    }
}

/**
 * Example class with constructor dependency.
 */
class ClassWithDependency
{
    public function __construct(public stdClass $dependency)
    {
    }
}

interface ExampleInterface
{
}

class ExampleImplementation implements ExampleInterface
{
}
