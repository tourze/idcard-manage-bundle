<?php

namespace Tourze\IdcardManageBundle\Tests;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\GBT2261\Gender;
use Tourze\IdcardManageBundle\Service\IdcardService;
use Tourze\PHPUnitSymfonyKernelTest\AbstractIntegrationTestCase;

/**
 * @internal
 */
#[CoversClass(IdcardService::class)]
#[RunTestsInSeparateProcesses]
final class IdcardServiceIntegrationTest extends AbstractIntegrationTestCase
{
    private IdcardService $idcardService;

    protected function onSetUp(): void
    {
        $this->idcardService = self::getService(IdcardService::class);
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
     * 测试服务接口功能
     */
    public function testIdcardValidation(): void
    {
        // 测试有效身份证
        $validIdcard = $this->getValidTestIdcard();
        $result = $this->idcardService->isValid($validIdcard);

        // 验证有效的身份证号应该返回 true
        $this->assertTrue($result, '有效的身份证号应该通过基础验证');
    }

    /**
     * 测试获取身份证生日功能
     */
    public function testGetBirthday(): void
    {
        $validIdcard = $this->getValidTestIdcard();
        $result = $this->idcardService->getBirthday($validIdcard);

        // 检查返回的是字符串或null
        $this->assertIsString($result);
        // 验证日期格式是否正确 (YYYY-MM-DD)
        $this->assertMatchesRegularExpression('/^\d{4}-\d{2}-\d{2}$/', $result);
    }

    /**
     * 测试获取身份证性别功能
     */
    public function testGetGender(): void
    {
        $validIdcard = $this->getValidTestIdcard();
        $result = $this->idcardService->getGender($validIdcard);

        // 验证返回了有效的性别枚举值
        $this->assertInstanceOf(Gender::class, $result);
    }

    /**
     * 测试二元素验证功能
     */
    public function testTwoElementVerify(): void
    {
        // 当前实现总是返回 false
        $this->assertFalse($this->idcardService->twoElementVerify('张三', $this->getValidTestIdcard()));
    }
}
