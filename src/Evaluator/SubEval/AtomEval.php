<?php

declare(strict_types=1);

namespace Elfennol\MonkeyPhp\Evaluator\SubEval;

use Elfennol\MonkeyPhp\Evaluator\EvaluatorException;
use Elfennol\MonkeyPhp\Evaluator\EvaluatorExceptionType;
use Elfennol\MonkeyPhp\Evaluator\RefSysObject;
use Elfennol\MonkeyPhp\Node\AtomExprNodeInterface;
use Elfennol\MonkeyPhp\Node\NodeType;
use Elfennol\MonkeyPhp\SysObject\Catalog\IntSysObject;
use Elfennol\MonkeyPhp\SysObject\Catalog\StringSysObject;
use Elfennol\MonkeyPhp\SysObject\SysObjectInterface;

readonly class AtomEval
{
    public function __construct(private RefSysObject $ref)
    {
    }

    public function evaluate(AtomExprNodeInterface $node): SysObjectInterface
    {
        return match ($node->type()) {
            NodeType::Int => new IntSysObject($node->value()),
            NodeType::Bool => 'true' === $node->value() ? $this->ref->true : $this->ref->false,
            NodeType::String => new StringSysObject($node->value()),
            default => throw new EvaluatorException(
                EvaluatorExceptionType::NodeNotSupported,
                ['nodeType' => $node],
                'Unable to evaluate atom node.'
            )
        };
    }
}
