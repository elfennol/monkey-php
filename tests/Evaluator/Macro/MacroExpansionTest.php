<?php

declare(strict_types=1);

namespace Elfennol\MonkeyPhp\Tests\Evaluator\Macro;

use Elfennol\MonkeyPhp\Evaluator\Macro\MacroExpansion;
use Elfennol\MonkeyPhp\SysObject\Catalog\MacroSysObject;
use Elfennol\MonkeyPhp\Tests\EvaluatorFactoryTrait;
use Elfennol\MonkeyPhp\Tests\LexerFactoryTrait;
use Elfennol\MonkeyPhp\Tests\ModifierFactoryTrait;
use Elfennol\MonkeyPhp\Tests\ParserFactoryTrait;
use Elfennol\MonkeyPhp\Utils\Option\None;
use Elfennol\MonkeyPhp\Utils\Option\Some;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class MacroExpansionTest extends TestCase
{
    use LexerFactoryTrait;
    use ParserFactoryTrait;
    use EvaluatorFactoryTrait;
    use ModifierFactoryTrait;

    private MacroExpansion $macroExpansion;

    protected function setUp(): void
    {
        $this->macroExpansion = new MacroExpansion($this->createModifier(), $this->createEvaluator());
    }

    public function testDefineMacro(): void
    {
        $input = <<<'INPUT'
let number = 1;
let function = fn(x, y) { x + y };
let myMacro = macro(x, y) { x + y; };
INPUT;

        $expectedMacroBody = <<<BODY
{
  ":BlockStmt": [
    {
      ":+": [
        {
          ":x": []
        },
        {
          ":y": []
        }
      ]
    }
  ]
}
BODY;

        $context = $this->createContext();
        $env = $context->env();
        $parser = $this->createParser();
        $ast = $parser->parse($this->createLexer($input));

        $newAst = $this->macroExpansion->defineMacros($ast, $context);
        self::assertCount(2, $newAst->stmts());
        self::assertInstanceOf(None::class, $env->get('number'));
        self::assertInstanceOf(None::class, $env->get('function'));
        self::assertInstanceOf(Some::class, $env->get('myMacro'));
        $macro = $env->get('myMacro')->unwrap();
        self::assertInstanceOf(MacroSysObject::class, $macro);
        $params = $macro->params();
        self::assertCount(2, $params);
        self::assertSame('x', $params[0]->name());
        self::assertSame('y', $params[1]->name());
        self::assertJsonStringEqualsJsonString($expectedMacroBody, (string)json_encode($macro->body()));
    }

    #[DataProvider('expandMacroProvider')]
    public function testExpandMacro(string $input, string $output): void
    {
        $context = $this->createContext();
        $parser = $this->createParser();
        $ast = $parser->parse($this->createLexer($input));

        $newAst = $this->macroExpansion->defineMacros($ast, $context);
        $expanded = $this->macroExpansion->expandMacros($newAst, $context);
        self::assertJsonStringEqualsJsonString($output, (string)json_encode($expanded));
    }

    /**
     * @return string[][]
     *
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public static function expandMacroProvider(): array
    {
        $inputInfix = <<<'INPUT'
let infixExpression = macro() { quote(1 + 2); };
infixExpression();
INPUT;
        $outputInfix = <<<'OUTPUT'
{
  ":Program": [
    {
      ":+": [
        {
          ":1": []
        },
        {
          ":2": []
        }
      ]
    }
  ]
}
OUTPUT;

        $inputReverse = <<<'INPUT'
let reverse = macro(a, b) { quote(unquote(b) - unquote(a)); };
reverse(2 + 2, 10 - 5);
INPUT;
        $outputReverse = <<<'OUTPUT'
{
  ":Program": [
    {
      ":-": [
        {
          ":-": [
            {
              ":10": []
            },
            {
              ":5": []
            }
          ]
        },
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
  ]
}
OUTPUT;

        $inputUnless = <<<'INPUT'
let unless = macro(condition, consequence, alternative) {
    quote(if (!(unquote(condition))) {
        unquote(consequence);
    } else {
        unquote(alternative);
    });
};
unless(10 > 5, echo("not greater"), echo("greater"));
INPUT;
        $outputUnless = <<<'OUTPUT'
{
  ":Program": [
    {
      ":IfNode": [
        {
          ":!": [
            {
              ":>": [
                {
                  ":10": []
                },
                {
                  ":5": []
                }
              ]
            }
          ]
        },
        {
          ":BlockStmt": [
            {
              ":FnCall": [
                {
                  ":echo": []
                },
                {
                  ":not greater": []
                }
              ]
            }
          ]
        },
        {
          ":BlockStmt": [
            {
              ":FnCall": [
                {
                  ":echo": []
                },
                {
                  ":greater": []
                }
              ]
            }
          ]
        }
      ]
    }
  ]
}
OUTPUT;
        return [
            [$inputInfix, $outputInfix],
            [$inputReverse, $outputReverse],
            [$inputUnless, $outputUnless],
        ];
    }
}
