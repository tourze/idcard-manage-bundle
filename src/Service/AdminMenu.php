<?php

declare(strict_types=1);

namespace Tourze\IdcardManageBundle\Service;

use Knp\Menu\ItemInterface;
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;
use Tourze\EasyAdminMenuBundle\Service\LinkGeneratorInterface;
use Tourze\EasyAdminMenuBundle\Service\MenuProviderInterface;
use Tourze\IdcardManageBundle\Entity\IdcardValidationLog;

#[Autoconfigure(public: true)]
readonly class AdminMenu implements MenuProviderInterface
{
    public function __construct(private LinkGeneratorInterface $linkGenerator)
    {
    }

    public function __invoke(ItemInterface $item): void
    {
        if (null === $item->getChild('系统管理')) {
            $item->addChild('系统管理');
        }

        $systemMenu = $item->getChild('系统管理');
        if (null !== $systemMenu) {
            $systemMenu
                ->addChild('身份证验证记录')
                ->setUri($this->linkGenerator->getCurdListPage(IdcardValidationLog::class))
                ->setAttribute('icon', 'fas fa-id-card')
            ;
        }
    }
}
