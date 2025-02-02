<?php

namespace Elfennol\MonkeyPhp\Tests;

use RuntimeException;

/**
 * @internal
 */
trait LoadFixturesTrait
{
    public function loadFixture(string $fixture): string
    {
        $path = sprintf('%s/fixtures/%s', __DIR__, $fixture);
        $fixture = file_get_contents($path);

        if (false === $fixture) {
            throw new RuntimeException(sprintf('Fixture not found in %s.', $path));
        }

        return $fixture;
    }
}
