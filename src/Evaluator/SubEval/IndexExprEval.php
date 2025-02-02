<?php

declare(strict_types=1);

namespace Elfennol\MonkeyPhp\Evaluator\SubEval;

use Elfennol\MonkeyPhp\Evaluator\EvaluatorException;
use Elfennol\MonkeyPhp\Evaluator\EvaluatorExceptionType;
use Elfennol\MonkeyPhp\Evaluator\EvaluatorInterface;
use Elfennol\MonkeyPhp\Node\Catalog\Expr\IndexExprNode;
use Elfennol\MonkeyPhp\SysObject\AtomSysObjectInterface;
use Elfennol\MonkeyPhp\SysObject\Catalog\ArraySysObject;
use Elfennol\MonkeyPhp\SysObject\Catalog\HashMapSysObject;
use Elfennol\MonkeyPhp\SysObject\Catalog\IntSysObject;
use Elfennol\MonkeyPhp\SysObject\Context\ContextInterface;
use Elfennol\MonkeyPhp\SysObject\HashKey;
use Elfennol\MonkeyPhp\SysObject\SysObjectInterface;

readonly class IndexExprEval
{
    public function __construct(private HashKey $hashKey)
    {
    }

    public function evaluate(
        EvaluatorInterface $evaluator,
        IndexExprNode $node,
        ContextInterface $context
    ): SysObjectInterface {
        $left = $evaluator->evaluate($node->left(), $context);
        $index = $evaluator->evaluate($node->index(), $context);

        return match (true) {
            $left instanceof ArraySysObject && $index instanceof IntSysObject => $this->evalArrayIndex(
                $node,
                $left,
                $index
            ),
            $left instanceof HashMapSysObject && $index instanceof AtomSysObjectInterface => $this->evalHashIndex(
                $node,
                $left,
                $index
            ),
            default => throw new EvaluatorException(
                EvaluatorExceptionType::SysObjectInvalid,
                ['node' => $node->debug()],
                'Invalid index.'
            )
        };
    }

    private function evalArrayIndex(
        IndexExprNode $node,
        ArraySysObject $array,
        IntSysObject $index
    ): AtomSysObjectInterface {
        if (!$index->isPositive()) {
            throw new EvaluatorException(
                EvaluatorExceptionType::WrongIndex,
                ['node' => $node->debug(), 'indexNodeValue' => $index->nodeValue(), 'indexType' => $index->type()],
                'Index must be positive.'
            );
        }

        if ($index->nativeValue() >= count($array->elements())) {
            throw new EvaluatorException(
                EvaluatorExceptionType::WrongIndex,
                [
                    'node' => $node->debug(),
                    'indexNodeValue' => $index->nodeValue(),
                    'indexType' => $index->type(),
                    'arrayCount' => count($array->elements())
                ],
                'Index too big.'
            );
        }

        return $array->elements()[$index->nativeValue()];
    }

    private function evalHashIndex(
        IndexExprNode $node,
        HashMapSysObject $hashMap,
        AtomSysObjectInterface $index
    ): AtomSysObjectInterface {
        $hashKeyIndex = $this->hashKey->hash($index);
        if (!isset($hashMap->elements()[$hashKeyIndex])) {
            throw new EvaluatorException(
                EvaluatorExceptionType::WrongIndex,
                ['node' => $node->debug(), 'indexValue' => $hashKeyIndex],
                'Index not available.'
            );
        }

        return $hashMap->elements()[$hashKeyIndex]->value();
    }
}
