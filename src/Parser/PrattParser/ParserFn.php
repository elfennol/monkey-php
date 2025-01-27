<?php

declare(strict_types=1);

namespace Elfennol\MonkeyPhp\Parser\PrattParser;

use Elfennol\MonkeyPhp\Parser\PrattParser\SubParser\InfixParserInterface;
use Elfennol\MonkeyPhp\Parser\PrattParser\SubParser\PostfixParserInterface;
use Elfennol\MonkeyPhp\Parser\PrattParser\SubParser\PrefixParserInterface;
use Elfennol\MonkeyPhp\Token\TokenType;
use Elfennol\MonkeyPhp\Utils\Option\None;
use Elfennol\MonkeyPhp\Utils\Option\Option;
use Elfennol\MonkeyPhp\Utils\Option\Some;

/**
 * @internal
 */
readonly class ParserFn
{
    public function __construct(
        /** @var array<string, PrefixParserInterface> */
        private array $prefixFns,
        /** @var array<string, InfixParserInterface> */
        private array $infixFns,
        /** @var array<string, PostfixParserInterface> */
        private array $postfixFns,
    ) {
    }

    /**
     * @return Option<PrefixParserInterface>
     */
    public function getPrefixFn(TokenType $tokenType): Option
    {
        if (!isset($this->prefixFns[$tokenType->value])) {
            return new None();
        }

        return new Some($this->prefixFns[$tokenType->value]);
    }

    /**
     * @return Option<InfixParserInterface>
     */
    public function getInfixFn(TokenType $tokenType): Option
    {
        if (!isset($this->infixFns[$tokenType->value])) {
            return new None();
        }

        return new Some($this->infixFns[$tokenType->value]);
    }

    /**
     * @return Option<PostfixParserInterface>
     */
    public function getPostfixFn(TokenType $tokenType): Option
    {
        if (!isset($this->postfixFns[$tokenType->value])) {
            return new None();
        }

        return new Some($this->postfixFns[$tokenType->value]);
    }
}
