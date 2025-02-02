<?php

declare(strict_types=1);

namespace Elfennol\MonkeyPhp\Tests\SysObject;

use Elfennol\MonkeyPhp\Node\Catalog\BlockNode;
use Elfennol\MonkeyPhp\SysObject\Catalog\ArraySysObject;
use Elfennol\MonkeyPhp\SysObject\Catalog\BoolSysObject;
use Elfennol\MonkeyPhp\SysObject\Catalog\BuiltinSysObject;
use Elfennol\MonkeyPhp\SysObject\Catalog\FnSysObject;
use Elfennol\MonkeyPhp\SysObject\Catalog\HashMapItemSysObject;
use Elfennol\MonkeyPhp\SysObject\Catalog\HashMapSysObject;
use Elfennol\MonkeyPhp\SysObject\Catalog\IntSysObject;
use Elfennol\MonkeyPhp\SysObject\Catalog\MacroBuiltinSysObject;
use Elfennol\MonkeyPhp\SysObject\Catalog\ReturnSysObject;
use Elfennol\MonkeyPhp\SysObject\Catalog\StringSysObject;
use Elfennol\MonkeyPhp\SysObject\Catalog\UnitSysObject;
use Elfennol\MonkeyPhp\SysObject\Context\Env;
use Elfennol\MonkeyPhp\SysObject\InvalidSysObjectException;
use Elfennol\MonkeyPhp\SysObject\SysObjectInterface;
use Elfennol\MonkeyPhp\SysObject\SysObjectType;
use Elfennol\MonkeyPhp\Token\Token;
use Elfennol\MonkeyPhp\Token\TokenType;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class SysObjectTest extends TestCase
{
    #[DataProvider('sysObjectProvider')]
    public function testSysObject(SysObjectInterface $sysObject, SysObjectType $type): void
    {
        self::assertSame($type, $sysObject->type());
    }

    public function testIntSysObjectWhenNotValid(): void
    {
        $this->expectException(InvalidSysObjectException::class);
        new IntSysObject('foo');
    }

    public function testBoolSysObjectWhenNotValid(): void
    {
        $this->expectException(InvalidSysObjectException::class);
        new BoolSysObject('foo');
    }

    /**
     * @return array{array{SysObjectInterface, SysObjectType}}
     */
    public static function sysObjectProvider(): array
    {
        return [
            [new ArraySysObject([new StringSysObject('foo')]), SysObjectType::Array],
            [new BoolSysObject('true'), SysObjectType::Bool],
            [
                new BuiltinSysObject(fn () => new UnitSysObject()),
                SysObjectType::Builtin
            ],
            [
                new FnSysObject([], new BlockNode(new Token(TokenType::String, 'foo', 1, 1), []), new Env()),
                SysObjectType::Function,
            ],
            [
                new HashMapItemSysObject(new StringSysObject('foo'), new StringSysObject('bar')),
                SysObjectType::HashMapItem
            ],
            [new HashMapSysObject([]), SysObjectType::HashMap],
            [new IntSysObject('42'), SysObjectType::Int],
            [
                new MacroBuiltinSysObject(fn () => new UnitSysObject()),
                SysObjectType::MacroBuiltin
            ],
            [new ReturnSysObject(new StringSysObject('foo')), SysObjectType::Return],
            [new UnitSysObject(), SysObjectType::Unit],
        ];
    }
}
