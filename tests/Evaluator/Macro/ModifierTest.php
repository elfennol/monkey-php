<?php

declare(strict_types=1);

namespace Elfennol\MonkeyPhp\Tests\Evaluator\Macro;

use Closure;
use Elfennol\MonkeyPhp\Evaluator\ModifierInterface;
use Elfennol\MonkeyPhp\Node\Catalog\Atom\IntNode;
use Elfennol\MonkeyPhp\Node\NodeInterface;
use Elfennol\MonkeyPhp\Tests\LexerFactoryTrait;
use Elfennol\MonkeyPhp\Tests\ModifierFactoryTrait;
use Elfennol\MonkeyPhp\Tests\ParserFactoryTrait;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class ModifierTest extends TestCase
{
    use LexerFactoryTrait;
    use ParserFactoryTrait;
    use ModifierFactoryTrait;

    private ModifierInterface $modifier;

    protected function setUp(): void
    {
        $this->modifier = $this->createModifier();
    }

    /**
     * @param Closure(NodeInterface): NodeInterface $action
     */
    #[DataProvider('modifyProvider')]
    public function testModify(string $input, Closure $action, string $expected): void
    {
        $parser = $this->createParser();
        $ast = $parser->parse($this->createLexer($input));
        $astModified = json_encode($this->modifier->modify($ast, $action), JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT);

        self::assertJsonStringEqualsJsonString($expected, $astModified);
    }

    /**
     * @return array{array{string, Closure(NodeInterface): NodeInterface, string}}
     */
    public static function modifyProvider(): array
    {
        $turnOneIntoTwo = static function (NodeInterface $node): NodeInterface {
            if (!$node instanceof IntNode) {
                return $node;
            }

            if ('1' !== $node->value()) {
                return $node;
            }

            return $node->buildWith('2');
        };

        return [
            ['1;', $turnOneIntoTwo, self::getExpectedAtom()],
            ['1 + 2;', $turnOneIntoTwo, self::getExpectedInfix()],
            ['-1;', $turnOneIntoTwo, self::getExpectedPrefix()],
            ['1!;', $turnOneIntoTwo, self::getExpectedPostfix()],
            ['[1];', $turnOneIntoTwo, self::getExpectedArray()],
            ['[1, 2, 3][1];', $turnOneIntoTwo, self::getExpectedIndexExpr()],
            ['if(true) { 1 };', $turnOneIntoTwo, self::getExpectedif()],
            ['if(true) { 1 } else { 1 };', $turnOneIntoTwo, self::getExpectedifElse()],
            ['let x = 1;', $turnOneIntoTwo, self::getExpectedLet()],
            ['x = 1;', $turnOneIntoTwo, self::getExpectedAssign()],
            ['fn(a) { 1 };', $turnOneIntoTwo, self::getExpectedFn()],
            ['return;', $turnOneIntoTwo, self::getExpectedReturnVoid()],
            ['return 1;', $turnOneIntoTwo, self::getExpectedReturn()],
            ['{1:1};', $turnOneIntoTwo, self::getExpectedHashMap()],
            ['fn() { 1 }();', $turnOneIntoTwo, self::getExpectedFnCall()],
        ];
    }

    private static function getExpectedAtom(): string
    {
        return <<<'JSON'
{
  ":Program": [
    {
      ":2": []
    }
  ]
}
JSON;
    }

    private static function getExpectedInfix(): string
    {
        return <<<'JSON'
{
  ":Program": [
    {
      ":+": [
        {
          ":2": []
        },
        {
          ":2": []
        }
      ]
    }
  ]
}
JSON;
    }

    private static function getExpectedPrefix(): string
    {
        return <<<'JSON'
{
  ":Program": [
    {
      ":-": [
        {
          ":2": []
        }
      ]
    }
  ]
}
JSON;
    }

    private static function getExpectedPostfix(): string
    {
        return <<<'JSON'
{
  ":Program": [
    {
      ":!": [
        {
          ":2": []
        }
      ]
    }
  ]
}
JSON;
    }

    private static function getExpectedArray(): string
    {
        return <<<'JSON'
{
  ":Program": [
    {
      ":Array": [
        {
          ":2": []
        }
      ]
    }
  ]
}
JSON;
    }

    private static function getExpectedIndexExpr(): string
    {
        return <<<'JSON'
{
  ":Program": [
    {
      ":IndexExpr": [
        {
          ":Array": [
            {
              ":2": []
            },
            {
              ":2": []
            },
            {
              ":3": []
            }
          ]
        },
        {
          ":2": []
        }
      ]
    }
  ]
}
JSON;
    }

    private static function getExpectedIf(): string
    {
        return <<<'JSON'
{
  ":Program": [
    {
      ":IfNode": [
        {
          ":true": []
        },
        {
          ":BlockStmt": [
            {
              ":2": []
            }
          ]
        }
      ]
    }
  ]
}
JSON;
    }

    private static function getExpectedifElse(): string
    {
        return <<<'JSON'
{
  ":Program": [
    {
      ":IfNode": [
        {
          ":true": []
        },
        {
          ":BlockStmt": [
            {
              ":2": []
            }
          ]
        },
        {
          ":BlockStmt": [
            {
              ":2": []
            }
          ]
        }
      ]
    }
  ]
}
JSON;
    }

    private static function getExpectedLet(): string
    {
        return <<<'JSON'
{
  ":Program": [
    {
      ":Let": [
        {
          ":x": []
        },
        {
          ":2": []
        }
      ]
    }
  ]
}
JSON;
    }

    private static function getExpectedAssign(): string
    {
        return <<<'JSON'
{
  ":Program": [
    {
      ":Assign": [
        {
          ":x": []
        },
        {
          ":2": []
        }
      ]
    }
  ]
}
JSON;
    }

    private static function getExpectedFn(): string
    {
        return <<<'JSON'
{
  ":Program": [
    {
      ":FnNode": [
        {
          ":FnParams": [
            {
              ":a": []
            }
          ]
        },
        {
          ":BlockStmt": [
            {
              ":2": []
            }
          ]
        }
      ]
    }
  ]
}
JSON;
    }

    private static function getExpectedReturnVoid(): string
    {
        return <<<'JSON'
{
  ":Program": [
    {
      ":Return": []
    }
  ]
}
JSON;
    }

    private static function getExpectedReturn(): string
    {
        return <<<'JSON'
{
  ":Program": [
    {
      ":Return": [
        {
          ":2": []
        }
      ]
    }
  ]
}
JSON;
    }

    private static function getExpectedHashMap(): string
    {
        return <<<'JSON'
{
  ":Program": [
    {
      ":HashMap": [
        {
          ":HashMapItem": [
            {
              ":2": []
            },
            {
              ":2": []
            }
          ]
        }
      ]
    }
  ]
}
JSON;
    }

    private static function getExpectedFnCall(): string
    {
        return <<<'JSON'
{
  ":Program": [
    {
      ":FnCall": [
        {
          ":FnNode": [
            {
              ":FnParams": []
            },
            {
              ":BlockStmt": [
                {
                  ":2": []
                }
              ]
            }
          ]
        }
      ]
    }
  ]
}
JSON;
    }
}
