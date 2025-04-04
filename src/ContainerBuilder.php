<?php

declare(strict_types=1);

namespace Elfennol\MonkeyPhp;

use Elfennol\MonkeyPhp\Compiler\Compiler;
use Elfennol\MonkeyPhp\Compiler\CompilerInterface;
use Elfennol\MonkeyPhp\SysObject\Builtins\BuiltinsBuilder;
use Elfennol\MonkeyPhp\Evaluator\Evaluator;
use Elfennol\MonkeyPhp\Evaluator\EvaluatorInterface;
use Elfennol\MonkeyPhp\Evaluator\Macro\MacroBuiltinsBuilder;
use Elfennol\MonkeyPhp\Evaluator\Macro\Modifier;
use Elfennol\MonkeyPhp\Evaluator\Macro\ModifierBuilder;
use Elfennol\MonkeyPhp\Evaluator\ModifierInterface;
use Elfennol\MonkeyPhp\Lexer\LexerBuilder;
use Elfennol\MonkeyPhp\Lexer\LexerBuilderInterface;
use Elfennol\MonkeyPhp\Parser\ExprParserInterface;
use Elfennol\MonkeyPhp\Parser\Parser;
use Elfennol\MonkeyPhp\Parser\ParserInterface;
use Elfennol\MonkeyPhp\Parser\PrattParser\ParserFn;
use Elfennol\MonkeyPhp\Parser\PrattParser\ParserFnBuilder;
use Elfennol\MonkeyPhp\Parser\PrattParser\PrattParser;
use Elfennol\MonkeyPhp\Parser\StmtParserInterface;
use Elfennol\MonkeyPhp\Parser\SubParser\StmtParser;
use Elfennol\MonkeyPhp\SysObject\Context\Builtins;
use Elfennol\MonkeyPhp\SysObject\Context\BuiltinsInterface;
use Elfennol\MonkeyPhp\SysObject\Context\Context;
use Elfennol\MonkeyPhp\SysObject\Context\ContextInterface;
use Elfennol\MonkeyPhp\SysObject\Context\Env;
use Elfennol\MonkeyPhp\SysObject\Context\EnvInterface;
use Elfennol\MonkeyPhp\SysObject\Context\MacroBuiltins;
use Elfennol\MonkeyPhp\SysObject\Context\MacroBuiltinsInterface;
use Elfennol\MonkeyPhp\Utils\Container\Container;
use Elfennol\MonkeyPhp\Utils\Option\None;
use Elfennol\MonkeyPhp\Utils\Option\Option;
use Elfennol\MonkeyPhp\Vm\Vm;
use Elfennol\MonkeyPhp\Vm\VmInterface;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
readonly class ContainerBuilder
{
    public function build(): Container
    {
        return new Container(
            [
                ParserFn::class =>
                    static fn (Container $container) => $container->get(ParserFnBuilder::class)->build(),
                Builtins::class =>
                    static fn (Container $container) => $container->get(BuiltinsBuilder::class)->build(),
                MacroBuiltins::class =>
                    static fn (Container $container) => $container->get(MacroBuiltinsBuilder::class)->build(),
                Modifier::class =>
                    static fn (Container $container) => $container->get(ModifierBuilder::class)->build()
            ],
            [
                LexerBuilderInterface::class => LexerBuilder::class,
                ParserInterface::class => Parser::class,
                StmtParserInterface::class => StmtParser::class,
                ExprParserInterface::class => PrattParser::class,
                EvaluatorInterface::class => Evaluator::class,
                ContextInterface::class => Context::class,
                EnvInterface::class => Env::class,
                Option::class => None::class,
                BuiltinsInterface::class => Builtins::class,
                MacroBuiltinsInterface::class => MacroBuiltins::class,
                ModifierInterface::class => Modifier::class,
                CompilerInterface::class => Compiler::class,
                VmInterface::class => Vm::class,
            ]
        );
    }
}
