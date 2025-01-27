<?php

declare(strict_types=1);

namespace Elfennol\MonkeyPhp\Evaluator\SubEval;

use Elfennol\MonkeyPhp\Evaluator\ContextInterface;
use Elfennol\MonkeyPhp\Evaluator\EvaluatorException;
use Elfennol\MonkeyPhp\Evaluator\EvaluatorExceptionType;
use Elfennol\MonkeyPhp\Evaluator\EvaluatorInterface;
use Elfennol\MonkeyPhp\Node\Catalog\Expr\FnCallNode;
use Elfennol\MonkeyPhp\SysObject\Catalog\BuiltinSysObject;
use Elfennol\MonkeyPhp\SysObject\Catalog\FnSysObject;
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

        $argsResult = [];
        foreach ($node->fnArgs() as $fnArg) {
            $argsResult[] = $evaluator->evaluate($fnArg, $context);
        }

        return match (true) {
            $function instanceof FnSysObject => $this->applyFunction($evaluator, $function, $argsResult, $context),
            $function instanceof BuiltinSysObject => $this->applyBuiltin($function, $argsResult, $node),
            default => throw new EvaluatorException(
                EvaluatorExceptionType::ExprNotCallable,
                ['node' => $node->debug()],
                'Unable to evaluate FnCall node.'
            )
        };
    }

    /**
     * @param SysObjectInterface[] $args
     */
    private function applyFunction(
        EvaluatorInterface $evaluator,
        FnSysObject $function,
        array $args,
        ContextInterface $context
    ): SysObjectInterface {
        $newContext = $context->fromEnv(new Some($function->env()));

        foreach ($function->params() as $index => $param) {
            $newContext->env()->set($param->name(), $args[$index]);
        }

        $evaluated = $evaluator->evaluate($function->body(), $newContext);
        if ($evaluated instanceof TerminusSysObjectInterface) {
            return $evaluated->value();
        }

        return $evaluated;
    }

    /**
     * @param SysObjectInterface[] $args
     */
    private function applyBuiltin(BuiltinSysObject $builtinSysObject, array $args, FnCallNode $node): SysObjectInterface
    {
        try {
            return $builtinSysObject->builtinFn()(...$args);
        } catch (EvaluatorException $exception) {
            throw new EvaluatorException(
                $exception->getType(),
                array_merge(['node' => $node->debug()], $exception->getContext())
            );
        }
    }
}
