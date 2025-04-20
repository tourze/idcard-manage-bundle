<?php

namespace Tourze\IdcardManageBundle\Service;

use Tourze\GBT2261\Gender;

/**
 * 身份证服务
 */
interface IdcardService
{
    /**
     * 检查身份证号码是否符合基础规范
     */
    public function isValid(string $number): bool;

    /**
     * 获取身份证号码的生日
     */
    public function getBirthday(string $number, string $sep = '-'): string|false;

    /**
     * 获取身份证号码的性别
     */
    public function getGender(string $number): Gender;

    /**
     * 检查身份证号码是否正确（二元素）
     */
    public function twoElementVerify(string $certName, string $certNo): bool;
}
