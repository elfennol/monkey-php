<?php

declare(strict_types=1);

namespace Elfennol\MonkeyPhp\Repl;

use Elfennol\MonkeyPhp\SysObject\AtomSysObjectInterface;
use Elfennol\MonkeyPhp\SysObject\SysObjectInterface;
use Elfennol\MonkeyPhp\Utils\Exception\ContextExceptionInterface;
use Stringable;

readonly class Writer
{
    private const string BANNER = 'Monkey programming language.';
    private const string PROMPT = 'monkey > ';
    private const string ERROR_PREFIX = 'Monkey error:';

    public function displayBanner(): void
    {
        echo self::BANNER;
        echo "\n";
    }

    public function getPrompt(): string
    {
        return self::PROMPT;
    }

    public function display(SysObjectInterface $sysObject): void
    {
        if ($sysObject instanceof AtomSysObjectInterface) {
            echo $sysObject->nodeValue();
        }

        if ($sysObject instanceof Stringable) {
            echo $sysObject;
        }

        echo "\n";
    }

    public function displayError(ContextExceptionInterface $exception): void
    {
        echo sprintf("%s %s: %s", self::ERROR_PREFIX, $exception->getType()->name, $exception->getMessage());
        echo "\n";
        echo json_encode($exception->getContext(), JSON_PRETTY_PRINT);
        echo "\n";
    }

    public function displayRuntimeError(string $msg): void
    {
        echo sprintf("%s %s", self::ERROR_PREFIX, $msg);
        echo "\n";
    }
}
