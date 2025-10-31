<?php

declare(strict_types=1);

namespace Tourze\IdcardManageBundle\Tests\Enum;

use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\EnumExtra\BadgeInterface;
use Tourze\EnumExtra\Itemable;
use Tourze\EnumExtra\Labelable;
use Tourze\EnumExtra\Selectable;
use Tourze\GBT2261\Gender;
use Tourze\IdcardManageBundle\Enum\GenderDecorator;
use Tourze\PHPUnitEnum\AbstractEnumTestCase;

/**
 * @internal
 */
#[CoversClass(GenderDecorator::class)]
final class GenderDecoratorTest extends AbstractEnumTestCase
{
    public function testGetLabel(): void
    {
        self::assertSame('未知性别', GenderDecorator::UNKNOWN->getLabel());
        self::assertSame('男', GenderDecorator::MAN->getLabel());
        self::assertSame('女', GenderDecorator::WOMAN->getLabel());
        self::assertSame('未说明', GenderDecorator::UNSPECIFIED->getLabel());
    }

    public function testGetBadge(): void
    {
        self::assertSame(BadgeInterface::SECONDARY, GenderDecorator::UNKNOWN->getBadge());
        self::assertSame(BadgeInterface::PRIMARY, GenderDecorator::MAN->getBadge());
        self::assertSame(BadgeInterface::INFO, GenderDecorator::WOMAN->getBadge());
        self::assertSame(BadgeInterface::WARNING, GenderDecorator::UNSPECIFIED->getBadge());
    }

    public function testGetOriginalGender(): void
    {
        self::assertSame(Gender::UNKNOWN, GenderDecorator::UNKNOWN->getOriginalGender());
        self::assertSame(Gender::MAN, GenderDecorator::MAN->getOriginalGender());
        self::assertSame(Gender::WOMAN, GenderDecorator::WOMAN->getOriginalGender());
        self::assertSame(Gender::UNSPECIFIED, GenderDecorator::UNSPECIFIED->getOriginalGender());
    }

    public function testFromGender(): void
    {
        self::assertSame(GenderDecorator::UNKNOWN, GenderDecorator::fromGender(Gender::UNKNOWN));
        self::assertSame(GenderDecorator::MAN, GenderDecorator::fromGender(Gender::MAN));
        self::assertSame(GenderDecorator::WOMAN, GenderDecorator::fromGender(Gender::WOMAN));
        self::assertSame(GenderDecorator::UNSPECIFIED, GenderDecorator::fromGender(Gender::UNSPECIFIED));
    }

    public function testGetCasesForEasyAdmin(): void
    {
        $cases = GenderDecorator::getCasesForEasyAdmin();

        self::assertCount(4, $cases, '应该返回4个性别选项');
        self::assertContains(GenderDecorator::UNKNOWN, $cases);
        self::assertContains(GenderDecorator::MAN, $cases);
        self::assertContains(GenderDecorator::WOMAN, $cases);
        self::assertContains(GenderDecorator::UNSPECIFIED, $cases);
    }

    public function testEnumValues(): void
    {
        self::assertSame(0, GenderDecorator::UNKNOWN->value);
        self::assertSame(1, GenderDecorator::MAN->value);
        self::assertSame(2, GenderDecorator::WOMAN->value);
        self::assertSame(9, GenderDecorator::UNSPECIFIED->value);
    }

    public function testToArray(): void
    {
        $result = GenderDecorator::UNKNOWN->toArray();
        self::assertArrayHasKey('value', $result);
        self::assertArrayHasKey('label', $result);
        self::assertSame(0, $result['value']);
        self::assertSame('未知性别', $result['label']);

        $result = GenderDecorator::MAN->toArray();
        self::assertArrayHasKey('value', $result);
        self::assertArrayHasKey('label', $result);
        self::assertSame(1, $result['value']);
        self::assertSame('男', $result['label']);

        $result = GenderDecorator::WOMAN->toArray();
        self::assertArrayHasKey('value', $result);
        self::assertArrayHasKey('label', $result);
        self::assertSame(2, $result['value']);
        self::assertSame('女', $result['label']);

        $result = GenderDecorator::UNSPECIFIED->toArray();
        self::assertArrayHasKey('value', $result);
        self::assertArrayHasKey('label', $result);
        self::assertSame(9, $result['value']);
        self::assertSame('未说明', $result['label']);
    }

    public function testImplementsInterfaces(): void
    {
        // Type safety already guaranteed by enum implementation declaration
        $decorator = GenderDecorator::MAN;

        // 验证方法返回非空值
        self::assertNotEmpty($decorator->getLabel(), 'getLabel() 应该返回非空字符串');
        self::assertNotEmpty($decorator->getBadge(), 'getBadge() 应该返回非空字符串');
        self::assertNotEmpty(GenderDecorator::getCasesForEasyAdmin(), 'getCasesForEasyAdmin() 应该返回非空数组');
    }
}
