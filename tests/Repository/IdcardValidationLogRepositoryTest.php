<?php

declare(strict_types=1);

namespace Tourze\IdcardManageBundle\Tests\Repository;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Symfony\Component\Security\Core\User\UserInterface;
use Tourze\GBT2261\Gender;
use Tourze\IdcardManageBundle\Entity\IdcardValidationLog;
use Tourze\IdcardManageBundle\Repository\IdcardValidationLogRepository;
use Tourze\PHPUnitSymfonyKernelTest\AbstractRepositoryTestCase;

/**
 * @internal
 */
#[CoversClass(IdcardValidationLogRepository::class)]
#[RunTestsInSeparateProcesses]
final class IdcardValidationLogRepositoryTest extends AbstractRepositoryTestCase
{
    private IdcardValidationLogRepository $repository;

    protected function onSetUp(): void
    {
        $this->repository = self::getService(IdcardValidationLogRepository::class);
    }

    protected function createNewEntity(): IdcardValidationLog
    {
        $log = new IdcardValidationLog();
        $log->setIdcardNumber('110101199001011234');
        $log->setIsValid(true);
        $log->setBirthday('1990-01-01');
        $log->setGender(Gender::MAN);
        $log->setValidationType('基础验证');
        $log->setSource('单元测试');

        return $log;
    }

    protected function getRepository(): IdcardValidationLogRepository
    {
        return $this->repository;
    }

    
    public function testCanSaveAndRetrieveIdcardValidationLog(): void
    {
        $log = new IdcardValidationLog();
        $log->setIdcardNumber('110101199001011234');
        $log->setIsValid(true);
        $log->setBirthday('1990-01-01');
        $log->setGender(Gender::MAN);
        $log->setValidationType('基础验证');
        $log->setSource('单元测试');

        $this->repository->save($log, true);
        $this->assertNotNull($log->getId());

        // 通过ID查找
        $foundLog = $this->repository->find($log->getId());
        $this->assertInstanceOf(IdcardValidationLog::class, $foundLog);
        $this->assertSame('110101199001011234', $foundLog->getIdcardNumber());
        $this->assertTrue($foundLog->isValid());
        $this->assertSame('1990-01-01', $foundLog->getBirthday());
        $this->assertSame(Gender::MAN, $foundLog->getGender());
    }

    public function testFindByIdcardNumber(): void
    {
        $idcardNumber = '110101199001015678';

        // 创建两条记录
        $log1 = new IdcardValidationLog();
        $log1->setIdcardNumber($idcardNumber);
        $log1->setIsValid(true);
        $log1->setValidationType('基础验证');
        $this->repository->save($log1, true);

        $log2 = new IdcardValidationLog();
        $log2->setIdcardNumber($idcardNumber);
        $log2->setIsValid(false);
        $log2->setValidationType('二元素验证');
        $this->repository->save($log2, true);

        // 查找
        $results = $this->repository->findByIdcardNumber($idcardNumber);

        $this->assertCount(2, $results);
        $this->assertSame($idcardNumber, $results[0]->getIdcardNumber());
        $this->assertSame($idcardNumber, $results[1]->getIdcardNumber());

        // 结果应该按创建时间倒序排列
        $this->assertTrue($results[0]->getCreateTime() >= $results[1]->getCreateTime());
    }

    public function testFindRecentValidations(): void
    {
        // 创建多条记录
        for ($i = 0; $i < 5; ++$i) {
            $log = new IdcardValidationLog();
            $log->setIdcardNumber("11010119900101123{$i}");
            $log->setIsValid(0 === $i % 2);
            $log->setValidationType('基础验证');
            $this->repository->save($log, false);
        }
        self::getEntityManager()->flush();

        // 查找最近的3条记录
        $results = $this->repository->findRecentValidations(3);
        $this->assertCount(3, $results);

        // 验证结果按创建时间倒序排列
        for ($i = 0; $i < count($results) - 1; ++$i) {
            $this->assertTrue(
                $results[$i]->getCreateTime() >= $results[$i + 1]->getCreateTime(),
                '记录应该按创建时间倒序排列'
            );
        }
    }

    public function testGetValidationStats(): void
    {
        // 创建测试数据：3条有效，2条无效
        for ($i = 0; $i < 3; ++$i) {
            $log = new IdcardValidationLog();
            $log->setIdcardNumber("11010119900101100{$i}");
            $log->setIsValid(true);
            $log->setValidationType('基础验证');
            $this->repository->save($log, false);
        }

        for ($i = 0; $i < 2; ++$i) {
            $log = new IdcardValidationLog();
            $log->setIdcardNumber("11010119900101200{$i}");
            $log->setIsValid(false);
            $log->setValidationType('基础验证');
            $this->repository->save($log, false);
        }

        self::getEntityManager()->flush();

        $stats = $this->repository->getValidationStats();

        $this->assertArrayHasKey('valid', $stats, '统计结果应该包含 valid 键');
        $this->assertArrayHasKey('invalid', $stats, '统计结果应该包含 invalid 键');
        $this->assertArrayHasKey('total', $stats, '统计结果应该包含 total 键');

        $this->assertGreaterThanOrEqual(3, $stats['valid'], '至少应该有3条有效记录');
        $this->assertGreaterThanOrEqual(2, $stats['invalid'], '至少应该有2条无效记录');
        $this->assertGreaterThanOrEqual(5, $stats['total'], '总记录数应该至少为5');
        $this->assertSame($stats['valid'] + $stats['invalid'], $stats['total'], 'total 应该等于 valid + invalid');
    }

    public function testCanRemoveIdcardValidationLog(): void
    {
        $log = new IdcardValidationLog();
        $log->setIdcardNumber('110101199001019999');
        $log->setIsValid(true);
        $log->setValidationType('基础验证');

        $this->repository->save($log, true);
        $id = $log->getId();
        $this->assertNotNull($id);

        // 确认记录存在
        $foundLog = $this->repository->find($id);
        $this->assertNotNull($foundLog);

        // 删除记录
        $this->repository->remove($log, true);

        // 确认记录已删除
        $foundLog = $this->repository->find($id);
        $this->assertNull($foundLog);
    }

    public function testFindByUser(): void
    {
        // 使用测试基类的方法创建正确的用户实体
        $user = $this->createUser('testuser1', 'password1', ['ROLE_USER']);
        $otherUser = $this->createUser('testuser2', 'password2', ['ROLE_USER']);

        // 创建两条记录属于同一用户
        $log1 = new IdcardValidationLog();
        $log1->setIdcardNumber('110101199001018888');
        $log1->setIsValid(true);
        $log1->setValidationType('基础验证');
        $log1->setUser($user);
        $this->repository->save($log1, false);

        $log2 = new IdcardValidationLog();
        $log2->setIdcardNumber('110101199001019999');
        $log2->setIsValid(false);
        $log2->setValidationType('二元素验证');
        $log2->setUser($user);
        $this->repository->save($log2, false);

        // 创建一条记录属于其他用户
        $log3 = new IdcardValidationLog();
        $log3->setIdcardNumber('110101199001017777');
        $log3->setIsValid(true);
        $log3->setValidationType('基础验证');
        $log3->setUser($otherUser);
        $this->repository->save($log3, true);

        // 查找
        $results = $this->repository->findByUser($user);

        $this->assertCount(2, $results);
        foreach ($results as $result) {
            $this->assertSame($user, $result->getUser());
        }

        // 结果应该按创建时间倒序排列
        $this->assertTrue($results[0]->getCreateTime() >= $results[1]->getCreateTime());
    }
}
