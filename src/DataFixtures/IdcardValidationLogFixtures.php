<?php

declare(strict_types=1);

namespace Tourze\IdcardManageBundle\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\Attribute\When;
use Tourze\GBT2261\Gender;
use Tourze\IdcardManageBundle\Entity\IdcardValidationLog;

/**
 * 身份证验证记录数据填充
 *
 * 创建测试用的身份证验证记录数据，包含有效和无效的身份证号码、
 * 不同性别的数据、不同验证类型和来源
 * 只在 test 和 dev 环境中加载
 */
#[When(env: 'test')]
#[When(env: 'dev')]
class IdcardValidationLogFixtures extends Fixture implements FixtureGroupInterface
{
    public const VALIDATION_LOG_REFERENCE_PREFIX = 'idcard-validation-log-';
    public const VALIDATION_LOG_COUNT = 50;

    // 测试用的有效身份证号码（虚拟）
    private const VALID_IDCARD_NUMBERS = [
        '11010519491231002X', // 北京市 男 1949-12-31
        '11010519901015001X', // 北京市 男 1990-10-15
        '110105199001154026', // 北京市 女 1990-01-15
        '310104198506234567', // 上海市 男 1985-06-23
        '31010419851123456X', // 上海市 女 1985-11-23
        '44030219880203789X', // 深圳市 男 1988-02-03
        '440302198802034567', // 深圳市 女 1988-02-03
        '37010219950815123X', // 济南市 男 1995-08-15
        '370102199508154578', // 济南市 女 1995-08-15
        '51010319921206789X', // 成都市 男 1992-12-06
    ];

    // 测试用的无效身份证号码
    private const INVALID_IDCARD_NUMBERS = [
        '11010519491231000X', // 校验码错误
        '000000000000000000', // 全零
        '123456789012345678', // 不合法地区码
        '11010519491399001X', // 不存在的日期
        '11010500000000001X', // 无效日期
        '110105194912310001', // 15位
        '1101051949123100', // 不足位数
        '11010519491231001XX', // 超出位数
        '11010519491231001A', // 字母错误
        '11010519501231001X', // 超未来年份
    ];

    private const VALIDATION_TYPES = [
        '二元素验证',
        '三元素验证',
        '四元素验证',
        '基础格式验证',
        '实名认证验证',
        '银行卡验证',
        '手机号验证',
        '照片比对验证',
        '活体检测验证',
        '企业认证验证',
    ];

    private const VALIDATION_SOURCES = [
        'web_form',
        'mobile_app',
        'api_gateway',
        'admin_panel',
        'batch_import',
        'third_party_sdk',
        'wechat_mini_program',
        'alipay_mini_program',
        'h5_page',
        'desktop_client',
    ];

    public function load(ObjectManager $manager): void
    {
        // 创建有效验证记录
        $this->createValidValidationLogs($manager);

        // 创建无效验证记录
        $this->createInvalidValidationLogs($manager);

        // 创建混合验证记录
        $this->createMixedValidationLogs($manager);

        $manager->flush();
    }

    private function createValidValidationLogs(ObjectManager $manager): void
    {
        foreach (self::VALID_IDCARD_NUMBERS as $index => $idcardNumber) {
            $log = new IdcardValidationLog();
            $log->setIdcardNumber($idcardNumber);
            $log->setIsValid(true);

            // 从身份证号解析性别和生日
            $this->parseIdcardInfo($log, $idcardNumber);

            // 设置验证详情
            $log->setValidationType($this->getRandomValidationType());
            $log->setValidationDetails($this->createValidationDetails(true));
            $log->setSource($this->getRandomSource());

            // 设置创建时间（过去1-90天内的随机时间）
            $daysAgo = mt_rand(1, 90);
            $hoursOffset = mt_rand(0, 23);
            $minutesOffset = mt_rand(0, 59);
            $secondsOffset = mt_rand(0, 59);

            $createTime = new \DateTimeImmutable(sprintf(
                '-%d days %02d:%02d:%02d',
                $daysAgo,
                $hoursOffset,
                $minutesOffset,
                $secondsOffset
            ));
            $log->setCreateTime($createTime);

            // 更新时间略晚于创建时间
            $updateOffset = mt_rand(0, 24 * 60); // 0-24小时
            $log->setUpdateTime($createTime->modify("+{$updateOffset} minutes"));

            $manager->persist($log);
            $this->addReference(self::VALIDATION_LOG_REFERENCE_PREFIX . 'valid-' . $index, $log);
        }
    }

    private function createInvalidValidationLogs(ObjectManager $manager): void
    {
        foreach (self::INVALID_IDCARD_NUMBERS as $index => $idcardNumber) {
            $log = new IdcardValidationLog();
            $log->setIdcardNumber($idcardNumber);
            $log->setIsValid(false);

            // 无效身份证号码不设置性别和生日
            $log->setGender(null);
            $log->setBirthday(null);

            // 设置验证详情
            $log->setValidationType($this->getRandomValidationType());
            $log->setValidationDetails($this->createValidationDetails(false));
            $log->setSource($this->getRandomSource());

            // 设置创建时间
            $daysAgo = mt_rand(1, 90);
            $hoursOffset = mt_rand(0, 23);
            $minutesOffset = mt_rand(0, 59);
            $secondsOffset = mt_rand(0, 59);

            $createTime = new \DateTimeImmutable(sprintf(
                '-%d days %02d:%02d:%02d',
                $daysAgo,
                $hoursOffset,
                $minutesOffset,
                $secondsOffset
            ));
            $log->setCreateTime($createTime);

            $updateOffset = mt_rand(0, 24 * 60); // 0-24小时
            $log->setUpdateTime($createTime->modify("+{$updateOffset} minutes"));

            $manager->persist($log);
            $this->addReference(self::VALIDATION_LOG_REFERENCE_PREFIX . 'invalid-' . $index, $log);
        }
    }

    private function createMixedValidationLogs(ObjectManager $manager): void
    {
        $remainingCount = self::VALIDATION_LOG_COUNT - count(self::VALID_IDCARD_NUMBERS) - count(self::INVALID_IDCARD_NUMBERS);

        for ($i = 0; $i < $remainingCount; ++$i) {
            $log = new IdcardValidationLog();

            // 随机选择有效或无效
            $isValid = mt_rand(1, 100) <= 70; // 70%概率为有效

            if ($isValid) {
                // 生成随机有效身份证号
                $idcardNumber = $this->generateRandomValidIdcard();
                $log->setIdcardNumber($idcardNumber);
                $log->setIsValid(true);
                $this->parseIdcardInfo($log, $idcardNumber);
                $log->setValidationDetails($this->createValidationDetails(true));
            } else {
                // 生成随机无效身份证号
                $idcardNumber = $this->generateRandomInvalidIdcard();
                $log->setIdcardNumber($idcardNumber);
                $log->setIsValid(false);
                $log->setGender(null);
                $log->setBirthday(null);
                $log->setValidationDetails($this->createValidationDetails(false));
            }

            $log->setValidationType($this->getRandomValidationType());
            $log->setSource($this->getRandomSource());

            // 设置随机时间
            $daysAgo = mt_rand(1, 365);
            $hoursOffset = mt_rand(0, 23);
            $minutesOffset = mt_rand(0, 59);
            $secondsOffset = mt_rand(0, 59);

            $createTime = new \DateTimeImmutable(sprintf(
                '-%d days %02d:%02d:%02d',
                $daysAgo,
                $hoursOffset,
                $minutesOffset,
                $secondsOffset
            ));
            $log->setCreateTime($createTime);

            $updateOffset = mt_rand(0, 48 * 60); // 0-48小时
            $log->setUpdateTime($createTime->modify("+{$updateOffset} minutes"));

            $manager->persist($log);
            $this->addReference(self::VALIDATION_LOG_REFERENCE_PREFIX . 'mixed-' . $i, $log);
        }
    }

    private function parseIdcardInfo(IdcardValidationLog $log, string $idcardNumber): void
    {
        if (18 === strlen($idcardNumber)) {
            // 解析生日
            $year = substr($idcardNumber, 6, 4);
            $month = substr($idcardNumber, 10, 2);
            $day = substr($idcardNumber, 12, 2);
            $birthday = sprintf('%s-%s-%s', $year, $month, $day);
            $log->setBirthday($birthday);

            // 解析性别（倒数第二位，奇数为男，偶数为女）
            $genderCode = (int) substr($idcardNumber, -2, 1);
            $gender = (1 === $genderCode % 2) ? Gender::MAN : Gender::WOMAN;
            $log->setGender($gender);
        }
    }

    private function generateRandomValidIdcard(): string
    {
        // 随机选择地区码
        $areaCodes = ['110105', '310104', '440302', '370102', '510103', '500101', '320104', '330106', '420106', '610103'];
        $areaCode = $areaCodes[array_rand($areaCodes)];

        // 随机生成年月日
        $year = mt_rand(1950, 2005);
        $month = str_pad((string) mt_rand(1, 12), 2, '0', STR_PAD_LEFT);
        $day = str_pad((string) mt_rand(1, 28), 2, '0', STR_PAD_LEFT); // 使用28避免月份天数问题

        // 随机生成顺序码
        $sequence = str_pad((string) mt_rand(1, 999), 3, '0', STR_PAD_LEFT);

        // 计算校验码
        $idcard17 = $areaCode . $year . $month . $day . $sequence;
        $checkCode = $this->calculateCheckCode($idcard17);

        return $idcard17 . $checkCode;
    }

    private function generateRandomInvalidIdcard(): string
    {
        $types = ['wrong_check', 'invalid_date', 'wrong_format', 'invalid_area'];
        $type = $types[array_rand($types)];

        switch ($type) {
            case 'wrong_check':
                $validIdcard = $this->generateRandomValidIdcard();

                // 修改校验码使其错误
                return substr($validIdcard, 0, 17) . 'X';

            case 'invalid_date':
                return '11010519991399' . str_pad((string) mt_rand(1, 999), 3, '0', STR_PAD_LEFT) . 'X';

            case 'wrong_format':
                return mt_rand(1, 100) <= 50
                    ? substr($this->generateRandomValidIdcard(), 0, 15) // 15位
                    : str_repeat((string) mt_rand(0, 9), mt_rand(16, 20)); // 错误长度

            case 'invalid_area':
                return '000000' . date('Ymd', mt_rand(strtotime('1950-01-01'), strtotime('2005-12-31')))
                    . str_pad((string) mt_rand(1, 999), 3, '0', STR_PAD_LEFT) . 'X';

            default:
                return '123456789012345678';
        }
    }

    private function calculateCheckCode(string $idcard17): string
    {
        $factor = [7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2];
        $checkCodes = ['1', '0', 'X', '9', '8', '7', '6', '5', '4', '3', '2'];

        $sum = 0;
        for ($i = 0; $i < 17; ++$i) {
            $sum += (int) $idcard17[$i] * $factor[$i];
        }

        return $checkCodes[$sum % 11];
    }

    private function getRandomValidationType(): string
    {
        return self::VALIDATION_TYPES[array_rand(self::VALIDATION_TYPES)];
    }

    private function getRandomSource(): string
    {
        return self::VALIDATION_SOURCES[array_rand(self::VALIDATION_SOURCES)];
    }

    private function createValidationDetails(bool $isValid): string
    {
        if ($isValid) {
            $details = [
                'result' => 'success',
                'score' => mt_rand(85, 100),
                'provider' => ['公安部', '银联', '运营商'][array_rand(['公安部', '银联', '运营商'])],
                'response_time_ms' => mt_rand(150, 800),
                'verification_id' => 'ver_' . uniqid(),
                'checks' => [
                    'format_valid' => true,
                    'checksum_valid' => true,
                    'region_valid' => true,
                    'date_valid' => true,
                ],
            ];
        } else {
            $errorReasons = [
                '身份证号码格式错误',
                '校验码不匹配',
                '地区代码无效',
                '出生日期无效',
                '号码长度不正确',
                '包含非法字符',
                '不存在的身份证号',
                '接口调用失败',
                '网络超时',
                '第三方服务异常',
            ];

            $details = [
                'result' => 'failed',
                'error' => $errorReasons[array_rand($errorReasons)],
                'error_code' => 'E' . str_pad((string) mt_rand(1001, 9999), 4, '0', STR_PAD_LEFT),
                'provider' => ['公安部', '银联', '运营商'][array_rand(['公安部', '银联', '运营商'])],
                'response_time_ms' => mt_rand(100, 5000),
                'verification_id' => 'ver_' . uniqid(),
                'checks' => [
                    'format_valid' => mt_rand(1, 100) <= 30,
                    'checksum_valid' => mt_rand(1, 100) <= 20,
                    'region_valid' => mt_rand(1, 100) <= 40,
                    'date_valid' => mt_rand(1, 100) <= 50,
                ],
            ];
        }

        $result = json_encode($details, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        if (false === $result) {
            throw new \RuntimeException('Failed to encode validation details to JSON');
        }

        return $result;
    }

    public static function getGroups(): array
    {
        return [
            'idcard',
            'validation',
        ];
    }
}
