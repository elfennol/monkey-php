<?php

declare(strict_types=1);

namespace Elfennol\MonkeyPhp\Evaluator\SubEval;

use Elfennol\MonkeyPhp\Evaluator\EvaluatorException;
use Elfennol\MonkeyPhp\Evaluator\EvaluatorExceptionType;
use Elfennol\MonkeyPhp\Evaluator\EvaluatorInterface;
use Elfennol\MonkeyPhp\Node\Catalog\Expr\PostfixOpNode;
use Elfennol\MonkeyPhp\SysObject\Catalog\IntSysObject;
use Elfennol\MonkeyPhp\SysObject\Context\ContextInterface;
use Elfennol\MonkeyPhp\SysObject\SysObjectInterface;
use Elfennol\MonkeyPhp\Token\TokenType;

readonly class PostfixOpEval
{
    public function evaluate(
        EvaluatorInterface $evaluator,
        PostfixOpNode $node,
        ContextInterface $context
    ): SysObjectInterface {
        $result = $evaluator->evaluate($node->operand(), $context);

        try {
            return $this->matchOperator($result, $node);
        } catch (EvaluatorException $evaluatorException) {
            $exceptionContext = array_merge($evaluatorException->getContext(), ['node' => $node->debug()]);
            throw new EvaluatorException(
                $evaluatorException->getType(),
                $exceptionContext,
                'Unable to evaluate postfix op node.',
            );
        }
    }

    private function matchOperator(SysObjectInterface $result, PostfixOpNode $node): SysObjectInterface
    {
        return match ($node->operator()) {
            TokenType::Bang => $this->evalFactorialOp($result),
            default => throw new EvaluatorException(
                EvaluatorExceptionType::PostfixOpNotSupported,
                ['sysObjectType' => $result->type()],
                ''
            )
        };
    }

    private function evalFactorialOp(SysObjectInterface $result): SysObjectInterface
    {
        return match (true) {
            $result instanceof IntSysObject => new IntSysObject((string)$this->factorial($result->nativeValue())),
            default => throw new EvaluatorException(
                EvaluatorExceptionType::PostfixOpNotSupported,
                ['sysObjectType' => $result->type()],
            )
        };
    }

    /**
     * Alternative with GMP extension: https://www.php.net/manual/en/function.gmp-fact.php
     */
    private function factorial(int $value): int
    {
        if ($value < 0) {
            throw new EvaluatorException(
                EvaluatorExceptionType::OperandInvalid,
                ['operand' => $value],
                'Operand must be positive.'
            );
        }

        if ($value <= 1) {
            return 1;
        }

        return $value * $this->factorial($value - 1);
    }
}
