<?php

declare(strict_types=1);

namespace Elfennol\MonkeyPhp\Repl;

use Elfennol\MonkeyPhp\Utils\Option\None;
use Elfennol\MonkeyPhp\Utils\Option\Option;
use Elfennol\MonkeyPhp\Utils\Option\Some;

readonly class Reader
{
    public function read(string $prompt): string|false
    {
        return readline($prompt);
    }

    public function addHistory(string $input): void
    {
        readline_add_history($input);
    }

    /**
     * @return Option<string|false>
     */
    public function readFromStdin(): Option
    {
        $sRead = [STDIN];
        $sWrite = null;
        $sExcept = null;
        $sResNb = stream_select($sRead, $sWrite, $sExcept, 0);
        if ($sResNb) {
            return new Some(stream_get_contents(STDIN));
        }

        return new None();
    }
}
