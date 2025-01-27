<?php

declare(strict_types=1);

namespace Elfennol\MonkeyPhp\Node;

use JsonSerializable;

enum NodeType implements JsonSerializable
{
    case Program;

    case BlockStmt;
    case BlockConsequenceStmt;
    case BlockAlternativeStmt;
    case BlockAlternativeConditionStmt;

    case Array;
    case Bool;
    case IndexExpr;
    case Int;
    case FnCall;
    case FnNode;
    case FnParams;
    case HashMap;
    case HashMapItem;
    case Identifier;
    case IfNode;
    case String;

    case InfixOp;
    case PostfixOp;
    case PrefixOp;

    case Assign;
    case Let;
    case Return;

    /**
     * @return array{name: string}
     */
    public function jsonSerialize(): array
    {
        return [
            'name' => $this->name,
        ];
    }
}
