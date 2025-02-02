<?php

declare(strict_types=1);

namespace Elfennol\MonkeyPhp\Evaluator\Macro;

use Elfennol\MonkeyPhp\Evaluator\EvaluatorException;
use Elfennol\MonkeyPhp\Evaluator\EvaluatorExceptionType;
use Elfennol\MonkeyPhp\Evaluator\EvaluatorInterface;
use Elfennol\MonkeyPhp\Evaluator\ModifierInterface;
use Elfennol\MonkeyPhp\Node\CallableExprInterface;
use Elfennol\MonkeyPhp\Node\Catalog\Expr\FnCallNode;
use Elfennol\MonkeyPhp\Node\Catalog\Expr\IdentifierNode;
use Elfennol\MonkeyPhp\Node\Catalog\Expr\MacroNode;
use Elfennol\MonkeyPhp\Node\Catalog\Stmt\LetNode;
use Elfennol\MonkeyPhp\Node\NodeInterface;
use Elfennol\MonkeyPhp\Node\ProgramNodeInterface;
use Elfennol\MonkeyPhp\SysObject\Catalog\MacroSysObject;
use Elfennol\MonkeyPhp\SysObject\Catalog\QuoteSysObject;
use Elfennol\MonkeyPhp\SysObject\Context\ContextInterface;
use Elfennol\MonkeyPhp\SysObject\Context\Env;
use Elfennol\MonkeyPhp\SysObject\Context\EnvInterface;
use Elfennol\MonkeyPhp\Utils\Option\None;
use Elfennol\MonkeyPhp\Utils\Option\Option;
use Elfennol\MonkeyPhp\Utils\Option\Some;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
readonly class MacroExpansion
{
    public function __construct(private ModifierInterface $modifier, private EvaluatorInterface $evaluator)
    {
    }

    public function defineMacros(ProgramNodeInterface $programNode, ContextInterface $context): ProgramNodeInterface
    {
        $definitions = [];
        $stmts = $programNode->stmts();

        foreach ($stmts as $index => $stmt) {
            if (true === $this->tryAddMacro($stmt, $context->env())) {
                $definitions[] = $index;
            }
        }

        foreach ($definitions as $definition) {
            unset($stmts[$definition]);
        }

        return $programNode->buildWith($stmts);
    }

    public function expandMacros(NodeInterface $node, ContextInterface $context): NodeInterface
    {
        return $this->modifier->modify($node, function (NodeInterface $node) use ($context): NodeInterface {
            if (!$node instanceof FnCallNode) {
                return $node;
            }

            $macroOption = $this->findMacroCall($node->fnExpr(), $context->env());
            if (true === $macroOption->isNone()) {
                return $node;
            }
            $macro = $macroOption->unwrap();

            $quoteArgs = $this->quoteArgs($node);
            $extendedEnv = $this->extendMacroEnv($context->env(), $macro, $quoteArgs);
            $evaluated = $this->evaluator->evaluate($macro->body(), $context->buildWithEnv(new Some($extendedEnv)));

            if (!$evaluated instanceof QuoteSysObject) {
                throw new EvaluatorException(
                    EvaluatorExceptionType::SysObjectInvalid,
                    ['sysObjectType' => $evaluated->type()]
                );
            }

            return $evaluated->node();
        });
    }

    private function tryAddMacro(NodeInterface $stmt, EnvInterface $env): bool
    {
        if (!($stmt instanceof LetNode && $stmt->expr() instanceof MacroNode)) {
            return false;
        }

        if (true === $env->get($stmt->identifier()->name())->isSome()) {
            throw new EvaluatorException(
                EvaluatorExceptionType::ContextIdentifierConflict,
                ['node' => $stmt->debug()],
                'Macro already defined.'
            );
        }

        $env->set(
            $stmt->identifier()->name(),
            new MacroSysObject($stmt->expr()->fnParams()->identifiers(), $stmt->expr()->body())
        );

        return true;
    }

    /**
     * @return Option<MacroSysObject>
     */
    private function findMacroCall(CallableExprInterface $callableExpr, EnvInterface $env): Option
    {
        if (!$callableExpr instanceof IdentifierNode) {
            return new None();
        }

        $envOption = $env->get($callableExpr->name());
        if ($envOption->isNone()) {
            return new None();
        }

        if (!$envOption->unwrap() instanceof MacroSysObject) {
            return new None();
        }

        return new Some($envOption->unwrap());
    }

    /**
     * @return QuoteSysObject[]
     */
    private function quoteArgs(FnCallNode $callNode): array
    {
        $quoteArgs = [];
        foreach ($callNode->fnArgs() as $arg) {
            $quoteArgs[] = new QuoteSysObject($arg);
        }

        return $quoteArgs;
    }

    /**
     * @param QuoteSysObject[] $args
     */
    private function extendMacroEnv(EnvInterface $env, MacroSysObject $macro, array $args): EnvInterface
    {
        $extendedEnv = new Env(new Some($env));
        $argIndex = 0;
        foreach ($macro->params() as $param) {
            $extendedEnv->set($param->name(), $args[$argIndex]);
            $argIndex++;
        }

        return $extendedEnv;
    }
}
