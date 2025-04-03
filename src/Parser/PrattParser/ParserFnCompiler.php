<?php

declare(strict_types=1);

namespace Elfennol\MonkeyPhp\Parser\PrattParser;

use Elfennol\MonkeyPhp\Parser\PrattParser\SubParser\InfixAssignParser;
use Elfennol\MonkeyPhp\Parser\PrattParser\SubParser\InfixFnCallParser;
use Elfennol\MonkeyPhp\Parser\PrattParser\SubParser\InfixIndexExprParser;
use Elfennol\MonkeyPhp\Parser\PrattParser\SubParser\InfixParser;
use Elfennol\MonkeyPhp\Parser\PrattParser\SubParser\PostfixParser;
use Elfennol\MonkeyPhp\Parser\PrattParser\SubParser\PrefixArrayParser;
use Elfennol\MonkeyPhp\Parser\PrattParser\SubParser\PrefixAtomParser;
use Elfennol\MonkeyPhp\Parser\PrattParser\SubParser\PrefixFnParser;
use Elfennol\MonkeyPhp\Parser\PrattParser\SubParser\PrefixGroupedParser;
use Elfennol\MonkeyPhp\Parser\PrattParser\SubParser\PrefixHashMapParser;
use Elfennol\MonkeyPhp\Parser\PrattParser\SubParser\PrefixIfParser;
use Elfennol\MonkeyPhp\Parser\PrattParser\SubParser\PrefixParser;
use Elfennol\MonkeyPhp\Token\TokenType;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects) Maybe later, group classes by type.
 */
readonly class ParserFnCompiler
{
    /**
     * @SuppressWarnings(PHPMD.ExcessiveParameterList) Maybe later, group classes by type.
     */
    public function __construct(
        private PrefixParser $prefixParser,
        private PrefixAtomParser $prefixAtomParser,
        private PrefixGroupedParser $prefixGroupedParser,
        private PrefixIfParser $prefixIfParser,
        private PrefixFnParser $prefixFnParser,
        private PrefixArrayParser $prefixArrayParser,
        private PrefixHashMapParser $prefixHashMapParser,
        private InfixParser $infixParser,
        private InfixFnCallParser $infixFnCallParser,
        private InfixAssignParser $infixAssignParser,
        private InfixIndexExprParser $infixIndexExprParser,
        private PostfixParser $postfixParser,
    ) {
    }

    public function compile(): ParserFn
    {
        return new ParserFn(
            [
                TokenType::Plus->value => $this->prefixParser,
                TokenType::Minus->value => $this->prefixParser,
                TokenType::Bang->value => $this->prefixParser,
                TokenType::Identifier->value => $this->prefixAtomParser,
                TokenType::Int->value => $this->prefixAtomParser,
                TokenType::String->value => $this->prefixAtomParser,
                TokenType::True->value => $this->prefixAtomParser,
                TokenType::False->value => $this->prefixAtomParser,
                TokenType::Lparen->value => $this->prefixGroupedParser,
                TokenType::If->value => $this->prefixIfParser,
                TokenType::Function->value => $this->prefixFnParser,
                TokenType::Macro->value => $this->prefixFnParser,
                TokenType::Lbracket->value => $this->prefixArrayParser,
                TokenType::Lbrace->value => $this->prefixHashMapParser,
            ],
            [
                TokenType::Plus->value => $this->infixParser,
                TokenType::Minus->value => $this->infixParser,
                TokenType::Asterisk->value => $this->infixParser,
                TokenType::Slash->value => $this->infixParser,
                TokenType::DoubleAsterisk->value => $this->infixParser,
                TokenType::Gt->value => $this->infixParser,
                TokenType::Lt->value => $this->infixParser,
                TokenType::Eq->value => $this->infixParser,
                TokenType::NotEq->value => $this->infixParser,
                TokenType::Lparen->value => $this->infixFnCallParser,
                TokenType::Lbracket->value => $this->infixIndexExprParser,
                TokenType::Assign->value => $this->infixAssignParser,
            ],
            [
                TokenType::Bang->value => $this->postfixParser,
            ]
        );
    }
}
