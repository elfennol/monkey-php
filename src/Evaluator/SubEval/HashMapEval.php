<?php

declare(strict_types=1);

namespace Elfennol\MonkeyPhp\Evaluator\SubEval;

use Elfennol\MonkeyPhp\Evaluator\EvaluatorException;
use Elfennol\MonkeyPhp\Evaluator\EvaluatorExceptionType;
use Elfennol\MonkeyPhp\Evaluator\EvaluatorInterface;
use Elfennol\MonkeyPhp\Node\Catalog\Expr\HashMapNode;
use Elfennol\MonkeyPhp\SysObject\AtomSysObjectInterface;
use Elfennol\MonkeyPhp\SysObject\Catalog\HashMapItemSysObject;
use Elfennol\MonkeyPhp\SysObject\Catalog\HashMapSysObject;
use Elfennol\MonkeyPhp\SysObject\Context\ContextInterface;
use Elfennol\MonkeyPhp\SysObject\HashKey;
use Elfennol\MonkeyPhp\SysObject\SysObjectInterface;

readonly class HashMapEval
{
    public function __construct(private HashKey $hashKey)
    {
    }

    public function evaluate(
        EvaluatorInterface $evaluator,
        HashMapNode $node,
        ContextInterface $context,
    ): SysObjectInterface {
        $elements = [];
        foreach ($node->elements() as $element) {
            $key = $evaluator->evaluate($element->key(), $context);
            if (!$key instanceof AtomSysObjectInterface) {
                throw new EvaluatorException(
                    EvaluatorExceptionType::NodeInvalid,
                    ['node' => $node->debug()],
                    'Unable to evaluate hashmap key node: elements must be atom.'
                );
            }

            $value = $evaluator->evaluate($element->value(), $context);
            if (!$value instanceof AtomSysObjectInterface) {
                throw new EvaluatorException(
                    EvaluatorExceptionType::NodeInvalid,
                    ['node' => $node->debug()],
                    'Unable to evaluate hashmap value node: elements must be atom.'
                );
            }

            $elements[$this->hashKey->hash($key)] = new HashMapItemSysObject($key, $value);
        }

        return new HashMapSysObject($elements);
    }
}
