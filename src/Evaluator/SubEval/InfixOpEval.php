<?php

declare(strict_types=1);

namespace Elfennol\MonkeyPhp\Evaluator\SubEval;

use Elfennol\MonkeyPhp\Evaluator\ContextInterface;
use Elfennol\MonkeyPhp\Evaluator\EvaluatorException;
use Elfennol\MonkeyPhp\Evaluator\EvaluatorExceptionType;
use Elfennol\MonkeyPhp\Evaluator\EvaluatorInterface;
use Elfennol\MonkeyPhp\Evaluator\RefSysObject;
use Elfennol\MonkeyPhp\Node\Catalog\Expr\InfixOpNode;
use Elfennol\MonkeyPhp\SysObject\Catalog\BoolSysObject;
use Elfennol\MonkeyPhp\SysObject\Catalog\IntSysObject;
use Elfennol\MonkeyPhp\SysObject\Catalog\StringSysObject;
use Elfennol\MonkeyPhp\SysObject\SysObjectInterface;
use Elfennol\MonkeyPhp\Token\TokenType;

readonly class InfixOpEval
{
    public function __construct(private RefSysObject $ref)
    {
    }

    public function evaluate(
        EvaluatorInterface $evaluator,
        InfixOpNode $node,
        ContextInterface $context
    ): SysObjectInterface {
        $leftResult = $evaluator->evaluate($node->leftOperand(), $context);
        $rightResult = $evaluator->evaluate($node->rightOperand(), $context);

        try {
            return $this->matchOperands($leftResult, $rightResult, $node);
        } catch (EvaluatorException $evaluatorException) {
            $exceptionContext = array_merge($evaluatorException->getContext(), ['node' => $node->debug()]);
            $msg = ['Unable to evaluate infix op node.'];
            if ('' !== $evaluatorException->getMessage()) {
                $msg[] = $evaluatorException->getMessage();
            }
            throw new EvaluatorException(
                $evaluatorException->getType(),
                $exceptionContext,
                implode(' ', $msg)
            );
        }
    }

    private function matchOperands(
        SysObjectInterface $leftResult,
        SysObjectInterface $rightResult,
        InfixOpNode $node
    ): SysObjectInterface {
        return match (true) {
            $leftResult instanceof IntSysObject && $rightResult instanceof IntSysObject => $this->evalIntegerOp(
                $node->operator(),
                $leftResult,
                $rightResult
            ),
            $leftResult instanceof BoolSysObject && $rightResult instanceof BoolSysObject => $this->evalBoolOp(
                $node->operator(),
                $leftResult,
                $rightResult
            ),
            $leftResult instanceof StringSysObject && $rightResult instanceof StringSysObject => $this->evalStringOp(
                $node->operator(),
                $leftResult,
                $rightResult
            ),
            default => throw new EvaluatorException(
                EvaluatorExceptionType::InfixOpNotSupported,
                [
                    'node' => $node->debug(),
                    'leftSysObjectType' => $leftResult->type(),
                    'rightSysObjectType' => $rightResult->type(),
                ],
            )
        };
    }

    private function evalIntegerOp(
        TokenType $operator,
        IntSysObject $leftResult,
        IntSysObject $rightResult,
    ): SysObjectInterface {
        return match ($operator) {
            TokenType::Plus => new IntSysObject((string)($leftResult->nativeValue() + $rightResult->nativeValue())),
            TokenType::Minus => new IntSysObject((string)($leftResult->nativeValue() - $rightResult->nativeValue())),
            TokenType::Asterisk => new IntSysObject(
                (string)($leftResult->nativeValue() * $rightResult->nativeValue())
            ),
            TokenType::Slash => new IntSysObject(
                (string)(intdiv($leftResult->nativeValue(), $rightResult->nativeValue()))
            ),
            TokenType::DoubleAsterisk => $this->intPow($leftResult, $rightResult),
            TokenType::Lt => $this->toSysObjectBool($leftResult->nativeValue() < $rightResult->nativeValue()),
            TokenType::Gt => $this->toSysObjectBool($leftResult->nativeValue() > $rightResult->nativeValue()),
            TokenType::Eq => $this->toSysObjectBool($leftResult->nativeValue() === $rightResult->nativeValue()),
            TokenType::NotEq => $this->toSysObjectBool($leftResult->nativeValue() !== $rightResult->nativeValue()),
            default => throw new EvaluatorException(
                EvaluatorExceptionType::InfixOpNotSupported,
                [
                    'operator' => $operator->name,
                    'leftSysObjectType' => $leftResult->type(),
                    'rightSysObjectType' => $rightResult->type(),
                ],
            )
        };
    }

    private function evalBoolOp(
        TokenType $operator,
        BoolSysObject $leftResult,
        BoolSysObject $rightResult,
    ): SysObjectInterface {
        return match ($operator) {
            TokenType::Eq => $this->toSysObjectBool($leftResult->nativeValue() === $rightResult->nativeValue()),
            TokenType::NotEq => $this->toSysObjectBool($leftResult->nativeValue() !== $rightResult->nativeValue()),
            default => throw new EvaluatorException(
                EvaluatorExceptionType::InfixOpNotSupported,
                [
                    'operator' => $operator->name,
                    'leftSysObjectType' => $leftResult->type(),
                    'rightSysObjectType' => $rightResult->type(),
                ],
            )
        };
    }

    private function evalStringOp(
        TokenType $operator,
        StringSysObject $leftResult,
        StringSysObject $rightResult,
    ): SysObjectInterface {
        return match ($operator) {
            TokenType::Plus => new StringSysObject(
                sprintf('%s%s', $leftResult->nativeValue(), $rightResult->nativeValue())
            ),
            TokenType::Eq => $this->toSysObjectBool($leftResult->nativeValue() === $rightResult->nativeValue()),
            TokenType::NotEq => $this->toSysObjectBool($leftResult->nativeValue() !== $rightResult->nativeValue()),
            default => throw new EvaluatorException(
                EvaluatorExceptionType::InfixOpNotSupported,
                [
                    'operator' => $operator->name,
                    'leftSysObjectType' => $leftResult->type(),
                    'rightSysObjectType' => $rightResult->type(),
                ],
            )
        };
    }

    private function intPow(IntSysObject $left, IntSysObject $right): IntSysObject
    {
        if (!$right->isPositive()) {
            throw new EvaluatorException(
                EvaluatorExceptionType::InfixOpNotSupported,
                [
                    'leftSysObjectType' => $left->type(),
                    'rightSysObjectType' => $right->type(),
                ],
                'Operand must be positive.'
            );
        }

        return new IntSysObject((string)($left->nativeValue() ** $right->nativeValue()));
    }

    private function toSysObjectBool(bool $value): BoolSysObject
    {
        return true === $value ? $this->ref->true : $this->ref->false;
    }
}
