<?php

declare(strict_types=1);

namespace Elfennol\MonkeyPhp\Evaluator\SubEval;

use Elfennol\MonkeyPhp\Evaluator\EvaluatorException;
use Elfennol\MonkeyPhp\Evaluator\EvaluatorExceptionType;
use Elfennol\MonkeyPhp\Evaluator\EvaluatorInterface;
use Elfennol\MonkeyPhp\Node\Catalog\Expr\FnCallNode;
use Elfennol\MonkeyPhp\SysObject\Catalog\BuiltinSysObject;
use Elfennol\MonkeyPhp\SysObject\Catalog\FnSysObject;
use Elfennol\MonkeyPhp\SysObject\Catalog\MacroBuiltinSysObject;
use Elfennol\MonkeyPhp\SysObject\Context\ContextInterface;
use Elfennol\MonkeyPhp\SysObject\SysObjectInterface;
use Elfennol\MonkeyPhp\SysObject\TerminusSysObjectInterface;
use Elfennol\MonkeyPhp\Utils\Option\Some;

readonly class FnCallEval
{
    public function evaluate(
        EvaluatorInterface $evaluator,
        FnCallNode $node,
        ContextInterface $context
    ): SysObjectInterface {
        $function = $evaluator->evaluate($node->fnExpr(), $context);

        return match (true) {
            $function instanceof FnSysObject => $this->applyFunction($evaluator, $node, $context, $function),
            $function instanceof BuiltinSysObject => $this->applyBuiltin($evaluator, $node, $context, $function),
            $function instanceof MacroBuiltinSysObject => $this->applyMacroBuiltin($node, $context, $function),
            default => throw new EvaluatorException(
                EvaluatorExceptionType::ExprNotCallable,
                ['node' => $node->debug()],
                'Unable to evaluate FnCall node.'
            )
        };
    }

    private function applyFunction(
        EvaluatorInterface $evaluator,
        FnCallNode $node,
        ContextInterface $context,
        FnSysObject $function
    ): SysObjectInterface {
        $newContext = $context->buildWithEnv(new Some($function->env()));

        foreach ($function->params() as $index => $param) {
            $newContext->env()->set($param->name(), $this->evaluateFnArgs($evaluator, $node, $context)[$index]);
        }

        $evaluated = $evaluator->evaluate($function->body(), $newContext);
        if ($evaluated instanceof TerminusSysObjectInterface) {
            return $evaluated->value();
        }

        return $evaluated;
    }

    private function applyBuiltin(
        EvaluatorInterface $evaluator,
        FnCallNode $node,
        ContextInterface $context,
        BuiltinSysObject $builtinSysObject
    ): SysObjectInterface {
        try {
            return $builtinSysObject->builtinFn()(...$this->evaluateFnArgs($evaluator, $node, $context));
        } catch (EvaluatorException $exception) {
            throw new EvaluatorException(
                $exception->getType(),
                array_merge(['node' => $node->debug()], $exception->getContext())
            );
        }
    }

    private function applyMacroBuiltin(
        FnCallNode $node,
        ContextInterface $context,
        MacroBuiltinSysObject $builtinSysObject
    ): SysObjectInterface {
        try {
            return $builtinSysObject->macroBuiltinFn()($context, ...$node->fnArgs());
        } catch (EvaluatorException $exception) {
            throw new EvaluatorException(
                $exception->getType(),
                array_merge(['node' => $node->debug()], $exception->getContext()),
                $exception->getMessage()
            );
        }
    }

    /**
     * @return SysObjectInterface[]
     */
    private function evaluateFnArgs(EvaluatorInterface $evaluator, FnCallNode $node, ContextInterface $context): array
    {
        $argsResult = [];
        foreach ($node->fnArgs() as $fnArg) {
            $argsResult[] = $evaluator->evaluate($fnArg, $context);
        }

        return $argsResult;
    }
}
