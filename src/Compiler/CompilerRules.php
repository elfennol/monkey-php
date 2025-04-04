<?php

declare(strict_types=1);

namespace Elfennol\MonkeyPhp\Compiler;

use Elfennol\MonkeyPhp\Compiler\Catalog\ArrayCompilerRule;
use Elfennol\MonkeyPhp\Compiler\Catalog\AtomCompilerRule;
use Elfennol\MonkeyPhp\Compiler\Catalog\BlockCompilerRule;
use Elfennol\MonkeyPhp\Compiler\Catalog\ConditionCompilerRule;
use Elfennol\MonkeyPhp\Compiler\Catalog\FnCallCompilerRule;
use Elfennol\MonkeyPhp\Compiler\Catalog\FnCompilerRule;
use Elfennol\MonkeyPhp\Compiler\Catalog\HashMapCompilerRule;
use Elfennol\MonkeyPhp\Compiler\Catalog\IdentifierCompilerRule;
use Elfennol\MonkeyPhp\Compiler\Catalog\IndexCompilerRule;
use Elfennol\MonkeyPhp\Compiler\Catalog\InfixOpCompilerRule;
use Elfennol\MonkeyPhp\Compiler\Catalog\LetCompilerRule;
use Elfennol\MonkeyPhp\Compiler\Catalog\PostfixOpCompilerRule;
use Elfennol\MonkeyPhp\Compiler\Catalog\PrefixOpCompilerRule;
use Elfennol\MonkeyPhp\Compiler\Catalog\ProgramCompilerRule;
use Elfennol\MonkeyPhp\Compiler\Catalog\ReturnCompilerRule;

readonly class CompilerRules
{
    public function __construct(
        private InfixOpCompilerRule $infixOpCompilerRule,
        private AtomCompilerRule $atomCompilerRule,
        private BlockCompilerRule $blockCompilerRule,
        private PrefixOpCompilerRule $prefixOpCompilerRule,
        private PostfixOpCompilerRule $postfixOpCompilerRule,
        private ConditionCompilerRule $conditionCompilerRule,
        private ProgramCompilerRule $programCompilerRule,
        private LetCompilerRule $letCompilerRule,
        private IdentifierCompilerRule $identifierCompilerRule,
        private ArrayCompilerRule $compilerRule,
        private HashMapCompilerRule $hashMapCompilerRule,
        private IndexCompilerRule $indexCompilerRule,
        private FnCompilerRule $fnCompilerRule,
        private FnCallCompilerRule $fnCallCompilerRule,
        private ReturnCompilerRule $returnCompilerRule,
    ) {
    }

    /**
     * @return CompilerRuleInterface[]
     */
    public function get(): array
    {
        /** @var CompilerRuleInterface[] */
        return array_values(get_object_vars($this));
    }
}
