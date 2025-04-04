<?php

declare(strict_types=1);

namespace Elfennol\MonkeyPhp\Tests\Code;

use Elfennol\MonkeyPhp\Coder\Definition;
use Elfennol\MonkeyPhp\Coder\Definitions;
use Elfennol\MonkeyPhp\Coder\OpCode;
use PHPUnit\Framework\TestCase;

class DefinitionsTest extends TestCase
{
    private Definitions $definitions;

    protected function setUp(): void
    {
        $this->definitions = new Definitions();
    }

    /**
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function testGet(): void
    {
        foreach ($this->definitions->get() as $opCode => $definition) {
            self::assertNotNull(OpCode::tryFrom($opCode));
            self::assertInstanceOf(Definition::class, $definition);
        }
    }
}
