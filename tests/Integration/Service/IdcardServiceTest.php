<?php

namespace Tourze\IdcardManageBundle\Tests\Integration\Service;

use PHPUnit\Framework\TestCase;
use Tourze\IdcardManageBundle\Service\IdcardService;

/**
 * IdcardService 接口的测试类
 * 
 * 此测试类仅用于满足 PHPStan 规则要求
 * 实际的功能测试在 IdcardServiceImplTest 和 IdcardServiceIntegrationTest 中进行
 */
class IdcardServiceTest extends TestCase
{
    public function testInterfaceExists(): void
    {
        $this->assertTrue(interface_exists(IdcardService::class));
    }

    public function testInterfaceHasRequiredMethods(): void
    {
        $reflection = new \ReflectionClass(IdcardService::class);
        
        $this->assertTrue($reflection->hasMethod('isValid'));
        $this->assertTrue($reflection->hasMethod('getBirthday'));
        $this->assertTrue($reflection->hasMethod('getGender'));
        $this->assertTrue($reflection->hasMethod('twoElementVerify'));
    }
}