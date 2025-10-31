<?php

namespace Tourze\IdcardManageBundle\Tests\Service;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Tourze\IdcardManageBundle\Service\IdcardService;

/**
 * @internal
 */
#[CoversClass(IdcardService::class)]
final class IdcardServiceTest extends TestCase
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
