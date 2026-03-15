<?php

declare(strict_types=1);

namespace Elfennol\MonkeyPhp\Evaluator\Macro;

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

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
readonly class ModifierBuilder
{
    /**
     * @return Modifier
     */
    public function build(): Modifier
    {
        return new Modifier([
            new ArrayModifierRule(),
            new AssignModifierRule(),
            new AtomModifierRule(),
            new BlockModifierRule(),
            new FnCallModifierRule(),
            new FnModifierRule(),
            new HashMapModifierRule(),
            new IdentifierModifierRule(),
            new IfModifierRule(),
            new IndexExprModifierRule(),
            new InfixModifierRule(),
            new LetModifierRule(),
            new PostfixModifierRule(),
            new PrefixModifierRule(),
            new ProgramModifierRule(),
            new ReturnModifierRule(),
        ]);
    }
}
