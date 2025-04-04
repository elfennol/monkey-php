<?php

declare(strict_types=1);

namespace Elfennol\MonkeyPhp\Repl;

use Elfennol\MonkeyPhp\Utils\Exception\ContextExceptionInterface;

readonly class Repl
{
    public function __construct(
        private Interpreter $interpreter,
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
                $sysObject = $this->interpreter->read($input);
            } catch (ContextExceptionInterface $exception) {
                $this->writer->displayError($exception);

                return;
            }

            $this->writer->display($sysObject);

            return;
        }

        $this->writer->displayBanner();
        while (true) {
            $input = $this->reader->read($this->writer->getPrompt());
            if (false === $input) {
                break;
            }
            $this->reader->addHistory($input);

            try {
                $sysObject = $this->interpreter->read($input);
            } catch (ContextExceptionInterface $exception) {
                $this->writer->displayError($exception);

                continue;
            }

            $this->writer->display($sysObject);
        }
    }
}
