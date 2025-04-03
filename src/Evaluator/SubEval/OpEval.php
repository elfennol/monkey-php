<?php

declare(strict_types=1);

namespace Elfennol\MonkeyPhp\Evaluator\SubEval;

use Elfennol\MonkeyPhp\Evaluator\EvaluatorException;
use Elfennol\MonkeyPhp\Evaluator\EvaluatorExceptionType;
use Elfennol\MonkeyPhp\Evaluator\EvaluatorInterface;
use Elfennol\MonkeyPhp\Node\Catalog\Expr\InfixOpNode;
use Elfennol\MonkeyPhp\Node\Catalog\Expr\PostfixOpNode;
use Elfennol\MonkeyPhp\Node\Catalog\Expr\PrefixOpNode;
use Elfennol\MonkeyPhp\Node\OpNodeInterface;
use Elfennol\MonkeyPhp\SysObject\Context\ContextInterface;
use Elfennol\MonkeyPhp\SysObject\SysObjectInterface;

readonly class OpEval
{
    public function __construct(
        private PrefixOpEval $prefixOpEval,
        private InfixOpEval $infixOpEval,
        private PostfixOpEval $postfixOpEval
    ) {
    }

    public function evaluate(
        EvaluatorInterface $evaluator,
        OpNodeInterface $node,
        ContextInterface $context
    ): SysObjectInterface {
        return match (true) {
            $node instanceof PrefixOpNode => $this->prefixOpEval->evaluate($evaluator, $node, $context),
            $node instanceof PostfixOpNode => $this->postfixOpEval->evaluate($evaluator, $node, $context),
            $node instanceof InfixOpNode => $this->infixOpEval->evaluate($evaluator, $node, $context),
            default => throw new EvaluatorException(
                EvaluatorExceptionType::NodeNotSupported,
                ['node' => $node->debug()],
                'Unable to evaluate op node.'
            )
        };
    }
}
