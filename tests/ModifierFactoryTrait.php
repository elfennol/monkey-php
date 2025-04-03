<?php

declare(strict_types=1);

namespace Elfennol\MonkeyPhp\Tests;

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
use Elfennol\MonkeyPhp\Evaluator\ModifierInterface;

/**
 * @internal
 */
trait ModifierFactoryTrait
{
    private function createModifier(): ModifierInterface
    {
        return new Modifier([
            new ProgramModifierRule(),
            new AtomModifierRule(),
            new InfixModifierRule(),
            new PrefixModifierRule(),
            new PostfixModifierRule(),
            new ArrayModifierRule(),
            new IndexExprModifierRule(),
            new BlockModifierRule(),
            new IfModifierRule(),
            new LetModifierRule(),
            new AssignModifierRule(),
            new IdentifierModifierRule(),
            new FnModifierRule(),
            new ReturnModifierRule(),
            new HashMapModifierRule(),
            new FnCallModifierRule(),
        ]);
    }
}
