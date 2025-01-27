<?php

namespace Elfennol\MonkeyPhp\Tests;

use Elfennol\MonkeyPhp\Parser\Eater\TokenEater;
use Elfennol\MonkeyPhp\Parser\ExprParserInterface;
use Elfennol\MonkeyPhp\Parser\Parser;
use Elfennol\MonkeyPhp\Parser\ParserInterface;
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

trait ParserFactoryTrait
{
    private function createParser(): ParserInterface
    {
        $tokenEater = new TokenEater();
        $stmtParser = new StmtParser(
            $tokenEater,
            new LetParser($tokenEater),
            new ReturnParser($tokenEater)
        );
        return new Parser(
            $stmtParser,
            $this->createPrattParser(),
        );
    }

    private function createPrattParser(): ExprParserInterface
    {
        $tokenEater = new TokenEater();
        $stmtParser = new StmtParser(
            $tokenEater,
            new LetParser($tokenEater),
            new ReturnParser($tokenEater)
        );

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

        return new PrattParser(
            new BindingPowerSet(),
            $parserFnCompiler->compile(),
        );
    }
}
