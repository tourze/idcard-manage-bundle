<?php

declare(strict_types=1);

namespace Tourze\IdcardManageBundle\Enum;

use Tourze\EnumExtra\BadgeInterface;
use Tourze\EnumExtra\Itemable;
use Tourze\EnumExtra\ItemTrait;
use Tourze\EnumExtra\Labelable;
use Tourze\EnumExtra\Selectable;
use Tourze\EnumExtra\SelectTrait;
use Tourze\GBT2261\Gender;

/**
 * Gender枚举装饰器，为EasyAdmin EnumField提供BadgeInterface支持
 * 包装了来自tourze/gb-t-2261包的Gender枚举
 */
enum GenderDecorator: int implements Labelable, Itemable, Selectable, BadgeInterface
{
    use ItemTrait;
    use SelectTrait;

    case UNKNOWN = 0;
    case MAN = 1;
    case WOMAN = 2;
    case UNSPECIFIED = 9;

    public function getLabel(): string
    {
        return $this->getOriginalGender()->getLabel();
    }

    public function getBadge(): string
    {
        return match ($this) {
            self::UNKNOWN => BadgeInterface::SECONDARY,
            self::MAN => BadgeInterface::PRIMARY,
            self::WOMAN => BadgeInterface::INFO,
            self::UNSPECIFIED => BadgeInterface::WARNING,
        };
    }

    /**
     * 获取对应的原始Gender枚举
     */
    public function getOriginalGender(): Gender
    {
        return Gender::from($this->value);
    }

    /**
     * 从原始Gender枚举创建装饰器
     */
    public static function fromGender(Gender $gender): self
    {
        return self::from($gender->value);
    }

    /**
     * 获取所有装饰器cases，用于EasyAdmin EnumField
     * @return array<self>
     */
    public static function getCasesForEasyAdmin(): array
    {
        return self::cases();
    }
}
