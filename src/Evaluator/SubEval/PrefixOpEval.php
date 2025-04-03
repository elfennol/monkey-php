<?php

declare(strict_types=1);

namespace Elfennol\MonkeyPhp\Evaluator\SubEval;

use Elfennol\MonkeyPhp\Evaluator\EvaluatorException;
use Elfennol\MonkeyPhp\Evaluator\EvaluatorExceptionType;
use Elfennol\MonkeyPhp\Evaluator\EvaluatorInterface;
use Elfennol\MonkeyPhp\Evaluator\RefSysObject;
use Elfennol\MonkeyPhp\Node\Catalog\Expr\PrefixOpNode;
use Elfennol\MonkeyPhp\SysObject\Catalog\IntSysObject;
use Elfennol\MonkeyPhp\SysObject\Context\ContextInterface;
use Elfennol\MonkeyPhp\SysObject\SysObjectInterface;
use Elfennol\MonkeyPhp\Token\TokenType;

readonly class PrefixOpEval
{
    public function __construct(private RefSysObject $ref)
    {
    }

    public function evaluate(
        EvaluatorInterface $evaluator,
        PrefixOpNode $node,
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
                'Unable to evaluate prefix op node.'
            );
        }
    }

    private function matchOperator(SysObjectInterface $result, PrefixOpNode $node): SysObjectInterface
    {
        return match ($node->operator()) {
            TokenType::Bang => $this->evalNotOp($result),
            TokenType::Minus => $this->evalMinusOp($result),
            TokenType::Plus => $this->evalPlusOp($result),
            default => throw new EvaluatorException(
                EvaluatorExceptionType::PrefixOpNotSupported,
                ['sysObjectType' => $result->type()],
            )
        };
    }

    private function evalMinusOp(SysObjectInterface $result): SysObjectInterface
    {
        return match (true) {
            $result instanceof IntSysObject => new IntSysObject((string)(-$result->nativeValue())),
            default => throw new EvaluatorException(
                EvaluatorExceptionType::PrefixOpNotSupported,
                ['sysObjectType' => $result->type()],
            )
        };
    }

    private function evalPlusOp(SysObjectInterface $result): SysObjectInterface
    {
        return match (true) {
            $result instanceof IntSysObject => new IntSysObject($result->nodeValue()),
            default => throw new EvaluatorException(
                EvaluatorExceptionType::PrefixOpNotSupported,
                ['sysObjectType' => $result->type()],
            )
        };
    }

    private function evalNotOp(SysObjectInterface $result): SysObjectInterface
    {
        return match ($result) {
            $this->ref->true => $this->ref->false,
            $this->ref->false => $this->ref->true,
            default => throw new EvaluatorException(
                EvaluatorExceptionType::PrefixOpNotSupported,
                ['sysObjectType' => $result->type()],
            )
        };
    }
}
