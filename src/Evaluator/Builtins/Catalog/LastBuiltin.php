<?php

declare(strict_types=1);

namespace Elfennol\MonkeyPhp\Evaluator\Builtins\Catalog;

use Elfennol\MonkeyPhp\Evaluator\BuiltinInterface;
use Elfennol\MonkeyPhp\Evaluator\Builtins\BuiltinName;
use Elfennol\MonkeyPhp\Evaluator\Builtins\Validator;
use Elfennol\MonkeyPhp\Evaluator\EvaluatorException;
use Elfennol\MonkeyPhp\Evaluator\EvaluatorExceptionType;
use Elfennol\MonkeyPhp\SysObject\AtomSysObjectInterface;
use Elfennol\MonkeyPhp\SysObject\Catalog\ArraySysObject;
use Elfennol\MonkeyPhp\SysObject\SysObjectInterface;

readonly class LastBuiltin implements BuiltinInterface
{
    public function __construct(private Validator $validator)
    {
    }

    public function exec(SysObjectInterface ...$args): AtomSysObjectInterface
    {
        $this->validator->assertArgsNumber(BuiltinName::Last, $args, 1);
        $this->validator->assertArgsType(BuiltinName::Last, $args[0], ArraySysObject::class);
        /** @var ArraySysObject $arg */
        $arg = $args[0];

        if (0 === count($arg->elements())) {
            throw new EvaluatorException(
                EvaluatorExceptionType::EmptySysObject,
                ['name' => BuiltinName::Last, 'args' => $args]
            );
        }

        return $arg->elements()[count($arg->elements()) - 1];
    }
}
