<?php

declare(strict_types=1);

namespace Elfennol\MonkeyPhp\Repl;

use Elfennol\MonkeyPhp\Utils\Exception\ContextExceptionInterface;
use Elfennol\MonkeyPhp\Utils\Option\None;

readonly class Repl
{
    public function __construct(
        private EngineInterface $engine,
        private Reader $reader,
        private Writer $writer,
    ) {
    }

    public function make(): void
    {
        $inputOption = $this->reader->readFromStdin();
        if ($inputOption->isSome()) {
            $input = $inputOption->unwrap();
            if (false === $input) {
                $this->writer->displayRuntimeError('Unable to get contents from stdin.');

                return;
            }

            try {
                $engineResult = $this->engine->read($input);
            } catch (ContextExceptionInterface $exception) {
                $this->writer->displayError($exception);

                return;
            }

            $this->writer->display($engineResult->sysObject);

            return;
        }

        $this->writer->displayBanner();
        $symbolTable = new None();
        $constants = new None();
        $globals = new None();
        while (true) {
            $input = $this->reader->read($this->writer->getPrompt());
            if (false === $input) {
                break;
            }
            $this->reader->addHistory($input);

            try {
                $engineResult = $this->engine->read($input, $symbolTable, $constants, $globals);
                $symbolTable = $engineResult->symbolTable;
                $constants = $engineResult->constants;
                $globals = $engineResult->globals;
            } catch (ContextExceptionInterface $exception) {
                $this->writer->displayError($exception);

                continue;
            }

            $this->writer->display($engineResult->sysObject);
        }
    }
}
