<?php

declare(strict_types=1);

namespace Elfennol\MonkeyPhp\Vm;

use Elfennol\MonkeyPhp\Vm\Catalog\OpAddRule;
use Elfennol\MonkeyPhp\Vm\Catalog\OpArrayRule;
use Elfennol\MonkeyPhp\Vm\Catalog\OpBangRule;
use Elfennol\MonkeyPhp\Vm\Catalog\OpCallRule;
use Elfennol\MonkeyPhp\Vm\Catalog\OpConstantRule;
use Elfennol\MonkeyPhp\Vm\Catalog\OpDivRule;
use Elfennol\MonkeyPhp\Vm\Catalog\OpEqualRule;
use Elfennol\MonkeyPhp\Vm\Catalog\OpFactorialRule;
use Elfennol\MonkeyPhp\Vm\Catalog\OpFalseRule;
use Elfennol\MonkeyPhp\Vm\Catalog\OpClosureRule;
use Elfennol\MonkeyPhp\Vm\Catalog\OpCurrentClosureRule;
use Elfennol\MonkeyPhp\Vm\Catalog\OpGetBuiltinRule;
use Elfennol\MonkeyPhp\Vm\Catalog\OpGetFreeRule;
use Elfennol\MonkeyPhp\Vm\Catalog\OpGetGlobalRule;
use Elfennol\MonkeyPhp\Vm\Catalog\OpGetLocalRule;
use Elfennol\MonkeyPhp\Vm\Catalog\OpGreaterThanRule;
use Elfennol\MonkeyPhp\Vm\Catalog\OpHashMapRule;
use Elfennol\MonkeyPhp\Vm\Catalog\OpIndexRule;
use Elfennol\MonkeyPhp\Vm\Catalog\OpJumpNotTruthyRule;
use Elfennol\MonkeyPhp\Vm\Catalog\OpJumpRule;
use Elfennol\MonkeyPhp\Vm\Catalog\OpMinusRule;
use Elfennol\MonkeyPhp\Vm\Catalog\OpMulRule;
use Elfennol\MonkeyPhp\Vm\Catalog\OpNotEqualRule;
use Elfennol\MonkeyPhp\Vm\Catalog\OpPopRule;
use Elfennol\MonkeyPhp\Vm\Catalog\OpPowRule;
use Elfennol\MonkeyPhp\Vm\Catalog\OpReturnRule;
use Elfennol\MonkeyPhp\Vm\Catalog\OpReturnValueRule;
use Elfennol\MonkeyPhp\Vm\Catalog\OpSetGlobalRule;
use Elfennol\MonkeyPhp\Vm\Catalog\OpSetLocalRule;
use Elfennol\MonkeyPhp\Vm\Catalog\OpSubRule;
use Elfennol\MonkeyPhp\Vm\Catalog\OpTrueRule;
use Elfennol\MonkeyPhp\Vm\Catalog\OpUnitRule;

readonly class VmRules
{
    public function __construct(
        private OpConstantRule $opConstantRule,
        private OpPopRule $opPopRule,
        private OpAddRule $opAddRule,
        private OpSubRule $opSubRule,
        private OpMulRule $opMulRule,
        private OpDivRule $opDivRule,
        private OpTrueRule $opTrueRule,
        private OpFalseRule $opFalseRule,
        private OpEqualRule $opEqualRule,
        private OpNotEqualRule $opNotEqualRule,
        private OpGreaterThanRule $opGreaterThanRule,
        private OpBangRule $opBangRule,
        private OpMinusRule $opMinusRule,
        private OpFactorialRule $opFactorialRule,
        private OpPowRule $opPowRule,
        private OpJumpRule $jumpRule,
        private OpJumpNotTruthyRule $jumpNotTruthyRule,
        private OpUnitRule $opUnitRule,
        private OpSetGlobalRule $opSetGlobalRule,
        private OpGetGlobalRule $opGetGlobalRule,
        private OpArrayRule $opArrayRule,
        private OpHashMapRule $opHashMapRule,
        private OpIndexRule $opIndexRule,
        private OpCallRule $opCallRule,
        private OpReturnValueRule $opReturnValueRule,
        private OpReturnRule $opReturnRule,
        private OpSetLocalRule $opSetLocalRule,
        private OpGetLocalRule $opGetLocalRule,
        private OpGetBuiltinRule $opGetBuiltinRule,
        private OpClosureRule $opClosureRule,
        private OpGetFreeRule $opGetFreeRule,
        private OpCurrentClosureRule $opCurrentClosureRule,
    ) {
    }

    /**
     * @return VmRuleInterface[]
     */
    public function get(): array
    {
        /** @var VmRuleInterface[] */
        return array_values(get_object_vars($this));
    }
}
