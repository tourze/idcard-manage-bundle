<?php

namespace Tourze\IdcardManageBundle\Service;

use Ionepub\Idcard;
use Symfony\Component\DependencyInjection\Attribute\AsAlias;
use Tourze\GBT2261\Gender;

#[AsAlias(id: IdcardService::class)]
class IdcardServiceImpl implements IdcardService
{
    public function isValid(string $number): bool
    {
        // 本地身份证校验
        /** @var Idcard $idcard */
        $idcard = Idcard::getInstance();
        $idcard->setId($number);

        return $idcard->check();
    }

    public function getBirthday(string $number, string $sep = '-'): string|false
    {
        /** @var Idcard $idcard */
        $idcard = Idcard::getInstance();
        $idcard->setId($number);

        return $idcard->getBirthday($sep);
    }

    public function getGender(string $number): Gender
    {
        /** @var Idcard $idcard */
        $idcard = Idcard::getInstance();
        $idcard->setId($number);
        $val = $idcard->getGender();

        return match ($val) {
            '男', 'male' => Gender::MAN,
            '女', 'female' => Gender::WOMAN,
            false, null, '' => Gender::UNKNOWN, // 处理无效身份证返回 false 的情况
            default => Gender::UNKNOWN,
        };
    }

    public function twoElementVerify(string $certName, string $certNo): bool
    {
        // 这里我们没办法判断，只能交给第三方API
        return false;
    }
}
