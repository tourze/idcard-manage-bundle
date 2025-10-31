<?php

declare(strict_types=1);

namespace Tourze\IdcardManageBundle\Tests\Service;

use Knp\Menu\ItemInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\EasyAdminMenuBundle\Service\MenuProviderInterface;
use Tourze\IdcardManageBundle\Service\AdminMenu;
use Tourze\PHPUnitSymfonyWebTest\AbstractEasyAdminMenuTestCase;

/**
 * @internal
 */
#[CoversClass(AdminMenu::class)]
#[RunTestsInSeparateProcesses]
final class AdminMenuTest extends AbstractEasyAdminMenuTestCase
{
    protected function onSetUp(): void
    {
        // 初始化测试环境
    }

    public function testCanCreateAdminMenu(): void
    {
        /** @var AdminMenu $adminMenu */
        $adminMenu = self::getService(AdminMenu::class);

        $this->assertInstanceOf(AdminMenu::class, $adminMenu);
    }

    public function testImplementsMenuProviderInterface(): void
    {
        /** @var AdminMenu $adminMenu */
        $adminMenu = self::getService(AdminMenu::class);

        $this->assertInstanceOf(MenuProviderInterface::class, $adminMenu);
    }

    public function testInvokeAddsMenuItemWhenSystemManagementExists(): void
    {
        /** @var AdminMenu $adminMenu */
        $adminMenu = self::getService(AdminMenu::class);

        $systemManagementChild = $this->createMock(ItemInterface::class);
        $idcardChild = $this->createMock(ItemInterface::class);

        $systemManagementChild->expects($this->once())
            ->method('addChild')
            ->with('身份证验证记录')
            ->willReturn($idcardChild)
        ;

        $idcardChild->expects($this->once())
            ->method('setUri')
            ->with(self::stringContains('IdcardValidationLog'))
            ->willReturn($idcardChild)
        ;

        $idcardChild->expects($this->once())
            ->method('setAttribute')
            ->with('icon', 'fas fa-id-card')
            ->willReturn($idcardChild)
        ;

        $rootItem = $this->createMock(ItemInterface::class);
        $rootItem->expects($this->exactly(2))
            ->method('getChild')
            ->with('系统管理')
            ->willReturn($systemManagementChild)
        ;

        $adminMenu($rootItem);
    }

    public function testInvokeCreatesSystemManagementWhenNotExists(): void
    {
        /** @var AdminMenu $adminMenu */
        $adminMenu = self::getService(AdminMenu::class);

        $systemManagementChild = $this->createMock(ItemInterface::class);
        $idcardChild = $this->createMock(ItemInterface::class);

        $systemManagementChild->expects($this->once())
            ->method('addChild')
            ->with('身份证验证记录')
            ->willReturn($idcardChild)
        ;

        $idcardChild->expects($this->once())
            ->method('setUri')
            ->with(self::stringContains('IdcardValidationLog'))
            ->willReturn($idcardChild)
        ;

        $idcardChild->expects($this->once())
            ->method('setAttribute')
            ->with('icon', 'fas fa-id-card')
            ->willReturn($idcardChild)
        ;

        $rootItem = $this->createMock(ItemInterface::class);

        // 第一次调用返回null（不存在），第二次返回创建的子项
        $rootItem->expects($this->exactly(2))
            ->method('getChild')
            ->with('系统管理')
            ->willReturnOnConsecutiveCalls(null, $systemManagementChild)
        ;

        $rootItem->expects($this->once())
            ->method('addChild')
            ->with('系统管理')
        ;

        $adminMenu($rootItem);
    }

    public function testIsReadonly(): void
    {
        /** @var AdminMenu $adminMenu */
        $adminMenu = self::getService(AdminMenu::class);

        $reflection = new \ReflectionClass($adminMenu);
        $this->assertTrue($reflection->isReadOnly(), 'AdminMenu类应该是readonly的');
    }

    public function testIsAutoconfiguredAsPublic(): void
    {
        /** @var AdminMenu $adminMenu */
        $adminMenu = self::getService(AdminMenu::class);

        $reflection = new \ReflectionClass($adminMenu);
        $attributes = $reflection->getAttributes();

        $hasAutoconfigure = false;
        foreach ($attributes as $attribute) {
            if ('Symfony\Component\DependencyInjection\Attribute\Autoconfigure' === $attribute->getName()) {
                $hasAutoconfigure = true;
                $args = $attribute->getArguments();
                $this->assertTrue($args['public'] ?? false, 'AdminMenu应该配置为public服务');
                break;
            }
        }

        $this->assertTrue($hasAutoconfigure, 'AdminMenu应该有Autoconfigure属性');
    }
}
