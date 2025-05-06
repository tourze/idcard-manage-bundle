<?php

namespace Tourze\IdcardManageBundle\Tests\DependencyInjection;

use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Tourze\IdcardManageBundle\DependencyInjection\IdcardManageExtension;
use Tourze\IdcardManageBundle\Service\IdcardService;
use Tourze\IdcardManageBundle\Service\IdcardServiceImpl;

class IdcardManageExtensionTest extends TestCase
{
    private IdcardManageExtension $extension;
    private ContainerBuilder $container;

    protected function setUp(): void
    {
        $this->extension = new IdcardManageExtension();
        $this->container = new ContainerBuilder();
    }

    /**
     * 测试扩展加载配置
     */
    public function testLoad(): void
    {
        $this->extension->load([], $this->container);

        // 验证服务定义已添加
        $this->assertTrue($this->container->hasDefinition(IdcardServiceImpl::class));

        // 验证服务别名已添加（通过接口可以访问服务）
        $this->assertTrue($this->container->hasAlias(IdcardService::class));

        // 获取服务定义并验证是否设置了自动装配和自动配置
        $definition = $this->container->getDefinition(IdcardServiceImpl::class);
        $this->assertTrue($definition->isAutowired());
        $this->assertTrue($definition->isAutoconfigured());
    }
}
