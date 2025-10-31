<?php

declare(strict_types=1);

namespace Tourze\IdcardManageBundle\Tests;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\IdcardManageBundle\IdcardManageBundle;
use Tourze\PHPUnitSymfonyKernelTest\AbstractBundleTestCase;

/**
 * @internal
 */
#[CoversClass(IdcardManageBundle::class)]
#[RunTestsInSeparateProcesses]
final class IdcardManageBundleTest extends AbstractBundleTestCase
{
}
