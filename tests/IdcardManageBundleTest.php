<?php

namespace Tourze\IdcardManageBundle\Tests;

use PHPUnit\Framework\TestCase;
use Tourze\IdcardManageBundle\IdcardManageBundle;

class IdcardManageBundleTest extends TestCase
{
    /**
     * 测试 Bundle 类实例化
     */
    public function testBundleInitialization(): void
    {
        $bundle = new IdcardManageBundle();
        $this->assertInstanceOf(IdcardManageBundle::class, $bundle);
    }
}
