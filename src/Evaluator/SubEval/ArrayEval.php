<?php

declare(strict_types=1);

namespace Elfennol\MonkeyPhp\Evaluator\SubEval;

use Elfennol\MonkeyPhp\Evaluator\EvaluatorException;
use Elfennol\MonkeyPhp\Evaluator\EvaluatorExceptionType;
use Elfennol\MonkeyPhp\Evaluator\EvaluatorInterface;
use Elfennol\MonkeyPhp\Node\Catalog\Expr\ArrayNode;
use Elfennol\MonkeyPhp\SysObject\AtomSysObjectInterface;
use Elfennol\MonkeyPhp\SysObject\Catalog\ArraySysObject;
use Elfennol\MonkeyPhp\SysObject\Context\ContextInterface;
use Elfennol\MonkeyPhp\SysObject\SysObjectInterface;

readonly class ArrayEval
{
    public function evaluate(
        EvaluatorInterface $evaluator,
        ArrayNode $node,
        ContextInterface $context
    ): SysObjectInterface {
        $elements = [];
        foreach ($node->elements() as $element) {
            $elementEvaluated = $evaluator->evaluate($element, $context);
            if (!$elementEvaluated instanceof AtomSysObjectInterface) {
                throw new EvaluatorException(
                    EvaluatorExceptionType::NodeInvalid,
                    ['node' => $node->debug()],
                    'Unable to evaluate array node: elements must be atom.'
                );
            }
            $elements[] = $elementEvaluated;
        }

        return new ArraySysObject($elements);
    }
}
