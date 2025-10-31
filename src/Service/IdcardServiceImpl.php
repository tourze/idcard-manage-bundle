<?php

namespace Tourze\IdcardManageBundle\Service;

use Ionepub\Idcard;
use Symfony\Component\DependencyInjection\Attribute\AsAlias;
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;
use Tourze\GBT2261\Gender;

#[AsAlias(id: IdcardService::class)]
#[Autoconfigure(public: true)]
class IdcardServiceImpl implements IdcardService
{
    public function isValid(string $number): bool
    {
        // 本地身份证校验
        $idcard = Idcard::getInstance();
        assert($idcard instanceof Idcard);
        $idcard->setId($number);

        return $idcard->check();
    }

    public function getBirthday(string $number, string $sep = '-'): string|false
    {
        $idcard = Idcard::getInstance();
        assert($idcard instanceof Idcard);
        $idcard->setId($number);

        return $idcard->getBirthday($sep);
    }

    public function getGender(string $number): Gender
    {
        $idcard = Idcard::getInstance();
        assert($idcard instanceof Idcard);
        $idcard->setId($number);
        $val = $idcard->getGender();

        return match ($val) {
            '男', 'male' => Gender::MAN,
            '女', 'female' => Gender::WOMAN,
            false => Gender::UNKNOWN, // 处理无效身份证返回 false 的情况
            default => Gender::UNKNOWN, // 处理其他语言模式返回的 int 值（0 或 1）
        };
    }

    public function twoElementVerify(string $certName, string $certNo): bool
    {
        // 这里我们没办法判断，只能交给第三方API
        return false;
    }
}
