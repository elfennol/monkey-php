<?php

declare(strict_types=1);

namespace Elfennol\MonkeyPhp\Lexer;

use Elfennol\MonkeyPhp\Token\Token;
use Elfennol\MonkeyPhp\Token\TokenType;
use Elfennol\MonkeyPhp\Utils\Char\Char;
use Elfennol\MonkeyPhp\Utils\Option\None;
use Elfennol\MonkeyPhp\Utils\Option\Option;
use Elfennol\MonkeyPhp\Utils\Option\Some;
use Elfennol\MonkeyPhp\Utils\String\StringIterator;

class Lexer implements LexerInterface
{
    private Token $currentToken;
    private int $position;
    private string $char;
    /** @var Option<string> */
    private Option $overflow;
    private int $line;
    private int $col;

    public function __construct(
        private readonly StringIterator $input,
        private readonly TokenBuilder $tokenBuilder,
    ) {
    }

    public function current(): Token
    {
        return $this->currentToken;
    }

    public function key(): int
    {
        return $this->position;
    }

    public function next(): void
    {
        $this->currentToken = $this->createCurrentToken();
        $this->nextChar();
    }

    public function rewind(): void
    {
        $this->position = 0;
        $this->overflow = new None();
        $this->line = 0;
        $this->col = 0;
        $this->input->rewind();
        $this->char = $this->input->current();
        $this->next();
    }

    public function valid(): bool
    {
        return !in_array($this->currentToken->type, [TokenType::Eof, TokenType::Illegal], true);
    }

    private function nextChar(): void
    {
        if ($this->overflow->isSome()) {
            $overflow = $this->overflow->unwrap();
            $this->overflow = new None();
            $this->char = $overflow;

            return;
        }

        $this->incLineCol();
        $this->input->next();
        $this->char = $this->input->current();
        ++$this->position;
    }

    private function createCurrentToken(): Token
    {
        $ignoredCharList = sprintf(
            '%s%s%s%s%s',
            Char::SPACE->value,
            Char::LF->value,
            Char::CR->value,
            Char::HT->value,
            Char::VT->value,
        );
        while ('' === trim($this->char, $ignoredCharList)) {
            $this->nextChar();
        }

        return match ($this->char) {
            Char::Equals->value, Char::ExclamationMark->value => $this->equalityMatch(),
            Char::Asterisk->value => $this->asteriskMatch(),
            Char::Plus->value,
            Char::Minus->value,
            Char::Slash->value,
            Char::LessThan->value,
            Char::GreaterThan->value,
            Char::Semicolon->value,
            Char::Colon->value,
            Char::LeftParen->value,
            Char::RightParen->value,
            Char::Comma->value,
            Char::LeftBrace->value,
            Char::RightBrace->value,
            Char::LeftBracket->value,
            Char::RightBracket->value => $this->tokenBuilder->makeSpecialString($this->char, $this->line, $this->col),
            "\0" => $this->eofMatch(),
            default => $this->defaultMatch(),
        };
    }

    private function readIdentifier(): string
    {
        $identifier = [];

        do {
            $identifier[] = $this->char;
            $this->nextChar();
        } while ($this->isFollowingCharOfIdentifier());

        $this->overflow = new Some($this->char);

        return implode('', $identifier);
    }

    private function readNumber(): string
    {
        $number = [];

        do {
            $number[] = $this->char;
            $this->nextChar();
        } while ($this->isDigit());

        $this->overflow = new Some($this->char);

        return implode('', $number);
    }

    private function readString(): string
    {
        $string = [];
        $this->nextChar();
        while (!in_array($this->char, [Char::DoubleQuotes->value, Char::Nul->value], true)) {
            if (Char::Backslash->value === $this->char) {
                $this->nextChar();
                if (Char::DoubleQuotes->value !== $this->char) {
                    $string[] = Char::Backslash->value;
                }
            }
            $string[] = $this->char;
            $this->nextChar();
        }

        if (Char::Nul->value === $this->char) {
            throw new LexerException(
                LexerExceptionType::StringMustBeEnclosed,
                ['char' => $this->char, 'line' => $this->line, 'col' => $this->col]
            );
        }

        return implode('', $string);
    }

    private function defaultMatch(): Token
    {
        $line = $this->line;
        $col = $this->col;

        if ($this->isFirstCharOfIdentifier()) {
            return $this->tokenBuilder->makeKeywordOrIdentifier($this->readIdentifier(), $line, $col);
        }

        if ($this->isDigit()) {
            return $this->tokenBuilder->makeFromType(TokenType::Int, $this->readNumber(), $line, $col);
        }

        if (Char::DoubleQuotes->value === $this->char) {
            return $this->tokenBuilder->makeFromType(TokenType::String, $this->readString(), $line, $col);
        }

        return $this->tokenBuilder->makeFromType(TokenType::Illegal, $this->char, $line, $col);
    }

    private function eofMatch(): Token
    {
        return $this->tokenBuilder->makeFromType(TokenType::Eof, $this->char, $this->line, $this->col);
    }

    private function asteriskMatch(): Token
    {
        return $this->doubleCharMatch([Char::Asterisk->value, Char::ExclamationMark->value]);
    }

    private function equalityMatch(): Token
    {
        return $this->doubleCharMatch([Char::Equals->value]);
    }

    /**
     * @param string[] $secondChars
     */
    private function doubleCharMatch(array $secondChars): Token
    {
        $line = $this->line;
        $col = $this->col;
        $previousChar = $this->char;
        $this->nextChar();

        if (in_array($this->char, $secondChars, true)) {
            return $this->tokenBuilder->makeSpecialString(sprintf('%s%s', $previousChar, $this->char), $line, $col);
        }

        $this->overflow = new Some($this->char);

        return $this->tokenBuilder->makeSpecialString($previousChar, $line, $col);
    }

    private function isFirstCharOfIdentifier(): bool
    {
        return 1 === preg_match('/^[a-zA-Z_]$/', $this->char);
    }

    private function isFollowingCharOfIdentifier(): bool
    {
        return 1 === preg_match('/^[a-zA-Z0-9_]$/', $this->char);
    }

    private function isDigit(): bool
    {
        return 1 === preg_match('/^[0-9]$/', $this->char);
    }

    private function incLineCol(): void
    {
        if (Char::LF->value === $this->char) {
            $this->col = 0;
            ++$this->line;

            return;
        }

        ++$this->col;
    }
}
