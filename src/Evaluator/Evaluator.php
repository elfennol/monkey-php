<?php

declare(strict_types=1);

namespace Elfennol\MonkeyPhp\Evaluator;

use Elfennol\MonkeyPhp\Evaluator\SubEval\ArrayEval;
use Elfennol\MonkeyPhp\Evaluator\SubEval\AssignEval;
use Elfennol\MonkeyPhp\Evaluator\SubEval\AtomEval;
use Elfennol\MonkeyPhp\Evaluator\SubEval\ConditionEval;
use Elfennol\MonkeyPhp\Evaluator\SubEval\FnCallEval;
use Elfennol\MonkeyPhp\Evaluator\SubEval\FnEval;
use Elfennol\MonkeyPhp\Evaluator\SubEval\HashMapEval;
use Elfennol\MonkeyPhp\Evaluator\SubEval\IdentifierEval;
use Elfennol\MonkeyPhp\Evaluator\SubEval\IndexExprEval;
use Elfennol\MonkeyPhp\Evaluator\SubEval\LetEval;
use Elfennol\MonkeyPhp\Evaluator\SubEval\OpEval;
use Elfennol\MonkeyPhp\Evaluator\SubEval\ReturnEval;
use Elfennol\MonkeyPhp\Evaluator\SubEval\StmtListEval;
use Elfennol\MonkeyPhp\Node\AtomExprNodeInterface;
use Elfennol\MonkeyPhp\Node\Catalog\Expr\ArrayNode;
use Elfennol\MonkeyPhp\Node\Catalog\Expr\AssignNode;
use Elfennol\MonkeyPhp\Node\Catalog\Expr\FnCallNode;
use Elfennol\MonkeyPhp\Node\Catalog\Expr\FnNode;
use Elfennol\MonkeyPhp\Node\Catalog\Expr\HashMapNode;
use Elfennol\MonkeyPhp\Node\Catalog\Expr\IdentifierNode;
use Elfennol\MonkeyPhp\Node\Catalog\Expr\IndexExprNode;
use Elfennol\MonkeyPhp\Node\Catalog\Stmt\LetNode;
use Elfennol\MonkeyPhp\Node\Catalog\Stmt\ReturnNode;
use Elfennol\MonkeyPhp\Node\ConditionalNodeInterface;
use Elfennol\MonkeyPhp\Node\NodeInterface;
use Elfennol\MonkeyPhp\Node\OpNodeInterface;
use Elfennol\MonkeyPhp\Node\StmtListNodeInterface;
use Elfennol\MonkeyPhp\SysObject\Context\ContextInterface;
use Elfennol\MonkeyPhp\SysObject\SysObjectInterface;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects) Easier to read with the whole match in one place.
 * @SuppressWarnings(PHPMD.ExcessiveParameterList) idem.
 */
readonly class Evaluator implements EvaluatorInterface
{
    public function __construct(
        private StmtListEval $stmtListEval,
        private AtomEval $atomEval,
        private ArrayEval $arrayEval,
        private HashMapEval $hashMapEval,
        private IndexExprEval $indexExprEval,
        private OpEval $opEval,
        private ConditionEval $conditionEval,
        private ReturnEval $returnEval,
        private LetEval $letEval,
        private AssignEval $assignEval,
        private IdentifierEval $identifierEval,
        private FnEval $fnEval,
        private FnCallEval $fnCallEval,
    ) {
    }

    public function evaluate(NodeInterface $node, ContextInterface $context): SysObjectInterface
    {
        return match (true) {
            $node instanceof StmtListNodeInterface => $this->stmtListEval->evaluate($this, $node, $context),
            $node instanceof AtomExprNodeInterface => $this->atomEval->evaluate($node),
            $node instanceof ArrayNode => $this->arrayEval->evaluate($this, $node, $context),
            $node instanceof HashMapNode => $this->hashMapEval->evaluate($this, $node, $context),
            $node instanceof IndexExprNode => $this->indexExprEval->evaluate($this, $node, $context),
            $node instanceof OpNodeInterface => $this->opEval->evaluate($this, $node, $context),
            $node instanceof ConditionalNodeInterface => $this->conditionEval->evaluate($this, $node, $context),
            $node instanceof ReturnNode => $this->returnEval->evaluate($this, $node, $context),
            $node instanceof LetNode => $this->letEval->evaluate($this, $node, $context),
            $node instanceof AssignNode => $this->assignEval->evaluate($this, $node, $context),
            $node instanceof IdentifierNode => $this->identifierEval->evaluate($node, $context),
            $node instanceof FnNode => $this->fnEval->evaluate($node, $context),
            $node instanceof FnCallNode => $this->fnCallEval->evaluate($this, $node, $context),
            default => throw new EvaluatorException(
                EvaluatorExceptionType::NodeNotSupported,
                ['node' => $node->debug()],
                'Unable to evaluate node.'
            )
        };
    }
}
