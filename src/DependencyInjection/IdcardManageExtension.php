<?php

namespace Tourze\IdcardManageBundle\DependencyInjection;

use Tourze\SymfonyDependencyServiceLoader\AutoExtension;

class IdcardManageExtension extends AutoExtension
{
    protected function getConfigDir(): string
    {
        return __DIR__ . '/../Resources/config';
    }
}
