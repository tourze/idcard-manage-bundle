<?php

declare(strict_types=1);

namespace Tourze\IdcardManageBundle\Tests\Service;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\GBT2261\Gender;
use Tourze\IdcardManageBundle\Service\IdcardService;
use Tourze\IdcardManageBundle\Service\IdcardServiceImpl;
use Tourze\PHPUnitSymfonyKernelTest\AbstractIntegrationTestCase;

/**
 * @internal
 */
#[CoversClass(IdcardServiceImpl::class)]
#[RunTestsInSeparateProcesses]
final class IdcardServiceImplTest extends AbstractIntegrationTestCase
{
    private IdcardService $idcardService;

    protected function onSetUp(): void
    {
        /** @var IdcardService $service */
        $service = self::getContainer()->get(IdcardService::class);
        $this->idcardService = $service;
    }

    /**
     * 获取有效的测试身份证号码
     */
    private function getValidTestIdcard(): string
    {
        // 使用固定的有效测试身份证号
        return '110101199003070003';
    }

    /**
     * 测试有效身份证号码验证
     */
    public function testIsValidWithValidIdcard(): void
    {
        $validIdcard = $this->getValidTestIdcard();
        $result = $this->idcardService->isValid($validIdcard);

        // 验证有效身份证返回 true
        $this->assertTrue($result);
    }

    /**
     * 测试无效身份证号码验证
     */
    public function testIsValidWithInvalidIdcard(): void
    {
        // 无效身份证号码
        $invalidIdcard = '123456';
        $result = $this->idcardService->isValid($invalidIdcard);

        // 验证无效身份证返回 false
        $this->assertFalse($result);
    }

    /**
     * 测试获取有效身份证的生日
     */
    public function testGetBirthdayWithValidIdcard(): void
    {
        $validIdcard = $this->getValidTestIdcard();
        $result = $this->idcardService->getBirthday($validIdcard);

        // 验证返回值是字符串且格式正确
        $this->assertIsString($result);
        $this->assertEquals('1990-03-07', $result);
        $this->assertMatchesRegularExpression('/^\d{4}-\d{2}-\d{2}$/', $result);
    }

    /**
     * 测试获取无效身份证的生日
     */
    public function testGetBirthdayWithInvalidIdcard(): void
    {
        // 无效身份证号码
        $invalidIdcard = '123456';
        $result = $this->idcardService->getBirthday($invalidIdcard);

        // 验证返回值是 false
        $this->assertFalse($result);
    }

    /**
     * 测试获取身份证性别
     */
    public function testGetGender(): void
    {
        $validIdcard = $this->getValidTestIdcard();
        $result = $this->idcardService->getGender($validIdcard);

        // 验证返回了有效的性别枚举值
        $this->assertInstanceOf(Gender::class, $result);
        // 根据身份证倒数第二位 0（偶数）应该是女性
        $this->assertEquals(Gender::WOMAN, $result);
    }

    /**
     * 测试身份证二元素验证
     */
    public function testTwoElementVerify(): void
    {
        // 根据实现，此方法总是返回 false
        $this->assertFalse($this->idcardService->twoElementVerify('张三', $this->getValidTestIdcard()));
        $this->assertFalse($this->idcardService->twoElementVerify('', ''));
    }

    /**
     * 测试服务依赖注入
     */
    public function testServiceCanBeInjected(): void
    {
        $this->assertInstanceOf(IdcardServiceImpl::class, $this->idcardService);
        $this->assertInstanceOf(IdcardService::class, $this->idcardService);
    }
}
