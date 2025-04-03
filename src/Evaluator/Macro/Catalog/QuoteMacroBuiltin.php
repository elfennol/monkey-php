<?php

declare(strict_types=1);

namespace Elfennol\MonkeyPhp\Evaluator\Macro\Catalog;

use Elfennol\MonkeyPhp\Evaluator\EvaluatorException;
use Elfennol\MonkeyPhp\Evaluator\EvaluatorExceptionType;
use Elfennol\MonkeyPhp\Evaluator\EvaluatorInterface;
use Elfennol\MonkeyPhp\Evaluator\Macro\MacroBuiltinInterface;
use Elfennol\MonkeyPhp\Evaluator\Macro\MacroBuiltinName;
use Elfennol\MonkeyPhp\Evaluator\ModifierInterface;
use Elfennol\MonkeyPhp\Node\Catalog\Atom\BoolNode;
use Elfennol\MonkeyPhp\Node\Catalog\Atom\IntNode;
use Elfennol\MonkeyPhp\Node\Catalog\Atom\StringNode;
use Elfennol\MonkeyPhp\Node\Catalog\Expr\FnCallNode;
use Elfennol\MonkeyPhp\Node\Catalog\Expr\IdentifierNode;
use Elfennol\MonkeyPhp\Node\NodeInterface;
use Elfennol\MonkeyPhp\SysObject\AtomSysObjectInterface;
use Elfennol\MonkeyPhp\SysObject\Catalog\QuoteSysObject;
use Elfennol\MonkeyPhp\SysObject\Context\ContextInterface;
use Elfennol\MonkeyPhp\SysObject\MacroSysObjectInterface;
use Elfennol\MonkeyPhp\SysObject\SysObjectInterface;
use Elfennol\MonkeyPhp\SysObject\SysObjectType;
use Elfennol\MonkeyPhp\Token\Token;
use Elfennol\MonkeyPhp\Token\TokenType;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
readonly class QuoteMacroBuiltin implements MacroBuiltinInterface
{
    public function __construct(private ModifierInterface $modifier, private EvaluatorInterface $evaluator)
    {
    }

    public function exec(ContextInterface $context, NodeInterface ...$args): SysObjectInterface
    {
        if (1 !== count($args)) {
            throw new EvaluatorException(
                EvaluatorExceptionType::FnWrongArgsNumber,
                ['name' => MacroBuiltinName::Quote->value, 'args' => $args],
                'Unable to evaluate quote.'
            );
        }

        return new QuoteSysObject($this->evalUnquoteCalls($context, $args[0]));
    }

    private function evalUnquoteCalls(ContextInterface $context, NodeInterface $node): NodeInterface
    {
        return $this->modifier->modify($node, function (NodeInterface $node) use ($context): NodeInterface {
            if (!$this->isUnquoteCall($node)) {
                return $node;
            }

            if (1 !== count($node->fnArgs())) {
                return $node;
            }

            return $this->objectToNode($this->evaluator->evaluate($node->fnArgs()[0], $context));
        });
    }

    /**
     * @phpstan-assert-if-true FnCallNode $node
     * @phpstan-assert-if-true IdentifierNode $node->fnExpr()
     */
    private function isUnquoteCall(NodeInterface $node): bool
    {
        return $node instanceof FnCallNode
            && $node->fnExpr() instanceof IdentifierNode
            && 'unquote' === $node->fnExpr()->name();
    }

    private function objectToNode(SysObjectInterface $sysObject): NodeInterface
    {
        if ($sysObject instanceof MacroSysObjectInterface) {
            return $sysObject->node();
        }

        if ($sysObject instanceof AtomSysObjectInterface) {
            return match ($sysObject->type()) {
                SysObjectType::Int => new IntNode(new Token(TokenType::Int, $sysObject->nodeValue(), 0, 0)),
                SysObjectType::Bool => true === $sysObject->nativeValue()
                    ? new BoolNode(new Token(TokenType::True, $sysObject->nodeValue(), 0, 0))
                    : new BoolNode(new Token(TokenType::False, $sysObject->nodeValue(), 0, 0)),
                SysObjectType::String => new StringNode(new Token(TokenType::String, $sysObject->nodeValue(), 0, 0)),
                default => throw new EvaluatorException(
                    EvaluatorExceptionType::SysObjectInvalid,
                    ['sysObjectType' => $sysObject->type(), 'sysObjectNodeValue' => $sysObject->nodeValue()]
                ),
            };
        }

        throw new EvaluatorException(
            EvaluatorExceptionType::SysObjectInvalid,
            ['sysObjectType' => $sysObject->type()]
        );
    }
}
