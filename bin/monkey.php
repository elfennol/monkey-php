<?php

declare(strict_types=1);

use Elfennol\MonkeyPhp\Evaluator\Builtins\BuiltinCompiler;
use Elfennol\MonkeyPhp\Evaluator\Builtins\Catalog\EchoBuiltin;
use Elfennol\MonkeyPhp\Evaluator\Builtins\Catalog\FirstBuiltin;
use Elfennol\MonkeyPhp\Evaluator\Builtins\Catalog\LastBuiltin;
use Elfennol\MonkeyPhp\Evaluator\Builtins\Catalog\LenBuiltin;
use Elfennol\MonkeyPhp\Evaluator\Builtins\Catalog\PushBuiltin;
use Elfennol\MonkeyPhp\Evaluator\Builtins\Catalog\RestBuiltin;
use Elfennol\MonkeyPhp\Evaluator\Builtins\Validator;
use Elfennol\MonkeyPhp\Evaluator\Evaluator;
use Elfennol\MonkeyPhp\Evaluator\Macro\Catalog\QuoteMacroBuiltin;
use Elfennol\MonkeyPhp\Evaluator\Macro\MacroBuiltinCompiler;
use Elfennol\MonkeyPhp\Evaluator\Macro\MacroExpansion;
use Elfennol\MonkeyPhp\Evaluator\Macro\Modifier;
use Elfennol\MonkeyPhp\Evaluator\Macro\ModifierRule\ArrayModifierRule;
use Elfennol\MonkeyPhp\Evaluator\Macro\ModifierRule\AssignModifierRule;
use Elfennol\MonkeyPhp\Evaluator\Macro\ModifierRule\AtomModifierRule;
use Elfennol\MonkeyPhp\Evaluator\Macro\ModifierRule\BlockModifierRule;
use Elfennol\MonkeyPhp\Evaluator\Macro\ModifierRule\FnCallModifierRule;
use Elfennol\MonkeyPhp\Evaluator\Macro\ModifierRule\FnModifierRule;
use Elfennol\MonkeyPhp\Evaluator\Macro\ModifierRule\HashMapModifierRule;
use Elfennol\MonkeyPhp\Evaluator\Macro\ModifierRule\IdentifierModifierRule;
use Elfennol\MonkeyPhp\Evaluator\Macro\ModifierRule\IfModifierRule;
use Elfennol\MonkeyPhp\Evaluator\Macro\ModifierRule\IndexExprModifierRule;
use Elfennol\MonkeyPhp\Evaluator\Macro\ModifierRule\InfixModifierRule;
use Elfennol\MonkeyPhp\Evaluator\Macro\ModifierRule\LetModifierRule;
use Elfennol\MonkeyPhp\Evaluator\Macro\ModifierRule\PostfixModifierRule;
use Elfennol\MonkeyPhp\Evaluator\Macro\ModifierRule\PrefixModifierRule;
use Elfennol\MonkeyPhp\Evaluator\Macro\ModifierRule\ProgramModifierRule;
use Elfennol\MonkeyPhp\Evaluator\Macro\ModifierRule\ReturnModifierRule;
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
use Elfennol\MonkeyPhp\Lexer\LexerBuilder;
use Elfennol\MonkeyPhp\Lexer\TokenBuilder;
use Elfennol\MonkeyPhp\Lexer\TokenTypeFinder;
use Elfennol\MonkeyPhp\Parser\Eater\TokenEater;
use Elfennol\MonkeyPhp\Parser\Parser;
use Elfennol\MonkeyPhp\Parser\PrattParser\BindingPowerSet;
use Elfennol\MonkeyPhp\Parser\PrattParser\ParserFnCompiler;
use Elfennol\MonkeyPhp\Parser\PrattParser\PrattParser;
use Elfennol\MonkeyPhp\Parser\PrattParser\SubParser\InfixAssignParser;
use Elfennol\MonkeyPhp\Parser\PrattParser\SubParser\InfixFnCallParser;
use Elfennol\MonkeyPhp\Parser\PrattParser\SubParser\InfixIndexExprParser;
use Elfennol\MonkeyPhp\Parser\PrattParser\SubParser\InfixParser;
use Elfennol\MonkeyPhp\Parser\PrattParser\SubParser\PostfixParser;
use Elfennol\MonkeyPhp\Parser\PrattParser\SubParser\PrefixArrayParser;
use Elfennol\MonkeyPhp\Parser\PrattParser\SubParser\PrefixAtomBuilder;
use Elfennol\MonkeyPhp\Parser\PrattParser\SubParser\PrefixAtomParser;
use Elfennol\MonkeyPhp\Parser\PrattParser\SubParser\PrefixFnParser;
use Elfennol\MonkeyPhp\Parser\PrattParser\SubParser\PrefixGroupedParser;
use Elfennol\MonkeyPhp\Parser\PrattParser\SubParser\PrefixHashMapParser;
use Elfennol\MonkeyPhp\Parser\PrattParser\SubParser\PrefixIfParser;
use Elfennol\MonkeyPhp\Parser\PrattParser\SubParser\PrefixParser;
use Elfennol\MonkeyPhp\Parser\SubParser\AssignParser;
use Elfennol\MonkeyPhp\Parser\SubParser\BlockStmtParser;
use Elfennol\MonkeyPhp\Parser\SubParser\LetParser;
use Elfennol\MonkeyPhp\Parser\SubParser\ReturnParser;
use Elfennol\MonkeyPhp\Parser\SubParser\StmtParser;
use Elfennol\MonkeyPhp\Repl\Interpreter;
use Elfennol\MonkeyPhp\Repl\Reader;
use Elfennol\MonkeyPhp\Repl\Repl;
use Elfennol\MonkeyPhp\Repl\Writer;
use Elfennol\MonkeyPhp\SysObject\Context\Context;
use Elfennol\MonkeyPhp\SysObject\Context\Env;
use Elfennol\MonkeyPhp\SysObject\HashKey;

require_once dirname(__DIR__) . '/vendor/autoload.php';

$tokenEater = new TokenEater();
$stmtParser = new StmtParser($tokenEater, new LetParser($tokenEater), new ReturnParser($tokenEater));
$bindingPowerSet = new BindingPowerSet();
$parserFnCompiler = new ParserFnCompiler(
    new PrefixParser($tokenEater),
    new PrefixAtomParser($tokenEater, new PrefixAtomBuilder()),
    new PrefixGroupedParser($tokenEater),
    new PrefixIfParser($tokenEater, new BlockStmtParser($tokenEater, $stmtParser)),
    new PrefixFnParser($tokenEater, new BlockStmtParser($tokenEater, $stmtParser)),
    new PrefixArrayParser($tokenEater),
    new PrefixHashMapParser($tokenEater),
    new InfixParser($tokenEater),
    new InfixFnCallParser($tokenEater),
    new InfixAssignParser(new AssignParser($tokenEater)),
    new InfixIndexExprParser($tokenEater),
    new PostfixParser($tokenEater),
);
$prattParser = new PrattParser(
    new BindingPowerSet(),
    $parserFnCompiler->compile(),
);
$refSysObject = new RefSysObject();
$builtinValidator = new Validator();
$hashKey = new HashKey();
$modifier = new Modifier([
    new ArrayModifierRule(),
    new AssignModifierRule(),
    new AtomModifierRule(),
    new BlockModifierRule(),
    new FnCallModifierRule(),
    new FnModifierRule(),
    new HashMapModifierRule(),
    new IdentifierModifierRule(),
    new IfModifierRule(),
    new IndexExprModifierRule(),
    new InfixModifierRule(),
    new LetModifierRule(),
    new PostfixModifierRule(),
    new PrefixModifierRule(),
    new ProgramModifierRule(),
    new ReturnModifierRule(),
]);
$evaluator = new Evaluator(
    new StmtListEval(),
    new AtomEval($refSysObject),
    new ArrayEval(),
    new HashMapEval($hashKey),
    new IndexExprEval($hashKey),
    new OpEval(
        new PrefixOpEval($refSysObject),
        new InfixOpEval($refSysObject),
        new PostfixOpEval()
    ),
    new ConditionEval($refSysObject),
    new ReturnEval(),
    new LetEval(),
    new AssignEval(),
    new IdentifierEval(),
    new FnEval(),
    new FnCallEval(),
);

$context = new Context(
    new Env(),
    (new BuiltinCompiler(
        new EchoBuiltin($builtinValidator),
        new FirstBuiltin($builtinValidator),
        new LastBuiltin($builtinValidator),
        new LenBuiltin($builtinValidator),
        new PushBuiltin($builtinValidator),
        new RestBuiltin($builtinValidator)
    ))->compile(),
    (new MacroBuiltinCompiler(
        new QuoteMacroBuiltin($modifier, $evaluator)
    ))->compile()
);

$macroExpansion = new MacroExpansion($modifier, $evaluator);

$interpreter = new Interpreter(
    new LexerBuilder(new TokenBuilder(new TokenTypeFinder())),
    new Parser($stmtParser, $prattParser),
    $evaluator,
    $context,
    $macroExpansion
);

(new Repl($interpreter, new Reader(), new Writer()))->make();
