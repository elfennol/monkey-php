<?php

declare(strict_types=1);

namespace Elfennol\MonkeyPhp\Tests;

use Elfennol\MonkeyPhp\Evaluator\Builtins\BuiltinCompiler;
use Elfennol\MonkeyPhp\Evaluator\Builtins\Catalog\EchoBuiltin;
use Elfennol\MonkeyPhp\Evaluator\Builtins\Catalog\FirstBuiltin;
use Elfennol\MonkeyPhp\Evaluator\Builtins\Catalog\LastBuiltin;
use Elfennol\MonkeyPhp\Evaluator\Builtins\Catalog\LenBuiltin;
use Elfennol\MonkeyPhp\Evaluator\Builtins\Catalog\PushBuiltin;
use Elfennol\MonkeyPhp\Evaluator\Builtins\Catalog\RestBuiltin;
use Elfennol\MonkeyPhp\Evaluator\Builtins\Validator;
use Elfennol\MonkeyPhp\Evaluator\Context;
use Elfennol\MonkeyPhp\Evaluator\ContextInterface;
use Elfennol\MonkeyPhp\Evaluator\Evaluator;
use Elfennol\MonkeyPhp\Evaluator\EvaluatorInterface;
use Elfennol\MonkeyPhp\Evaluator\RefSysObject;
use Elfennol\MonkeyPhp\Evaluator\SubEval\ArrayEval;
use Elfennol\MonkeyPhp\Evaluator\SubEval\AssignEval;
use Elfennol\MonkeyPhp\Evaluator\SubEval\AtomEval;
use Elfennol\MonkeyPhp\Evaluator\SubEval\ConditionEval;
use Elfennol\MonkeyPhp\Evaluator\SubEval\FnCallEval;
use Elfennol\MonkeyPhp\Evaluator\SubEval\FnEval;
use Elfennol\MonkeyPhp\Evaluator\SubEval\HashMapEval;
use Elfennol\MonkeyPhp\Evaluator\SubEval\IdentifierEval;
use Elfennol\MonkeyPhp\Evaluator\SubEval\IndexExprEval;
use Elfennol\MonkeyPhp\Evaluator\SubEval\InfixOpEval;
use Elfennol\MonkeyPhp\Evaluator\SubEval\LetEval;
use Elfennol\MonkeyPhp\Evaluator\SubEval\OpEval;
use Elfennol\MonkeyPhp\Evaluator\SubEval\PostfixOpEval;
use Elfennol\MonkeyPhp\Evaluator\SubEval\PrefixOpEval;
use Elfennol\MonkeyPhp\Evaluator\SubEval\ReturnEval;
use Elfennol\MonkeyPhp\Evaluator\SubEval\StmtListEval;
use Elfennol\MonkeyPhp\SysObject\Environment\Env;
use Elfennol\MonkeyPhp\SysObject\HashKey;

trait EvaluatorFactoryTrait
{
    private function createEvaluator(): EvaluatorInterface
    {
        $refSysObject = new RefSysObject();
        $hashKey = new HashKey();

        return new Evaluator(
            new StmtListEval(),
            new AtomEval($refSysObject),
            new ArrayEval(),
            new HashMapEval($hashKey),
            new IndexExprEval($hashKey),
            new OpEval(new PrefixOpEval($refSysObject), new InfixOpEval($refSysObject), new PostfixOpEval()),
            new ConditionEval($refSysObject),
            new ReturnEval(),
            new LetEval(),
            new AssignEval(),
            new IdentifierEval(),
            new FnEval(),
            new FnCallEval(),
        );
    }

    private function createContext(): ContextInterface
    {
        $builtinValidator = new Validator();

        return new Context(
            new Env(),
            (new BuiltinCompiler(
                new EchoBuiltin($builtinValidator),
                new FirstBuiltin($builtinValidator),
                new LastBuiltin($builtinValidator),
                new LenBuiltin($builtinValidator),
                new PushBuiltin($builtinValidator),
                new RestBuiltin($builtinValidator),
            ))->compile()
        );
    }
}
