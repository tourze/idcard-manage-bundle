<?php

declare(strict_types=1);

namespace Tourze\IdcardManageBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Tourze\IdcardManageBundle\Entity\IdcardValidationLog;
use Tourze\PHPUnitSymfonyKernelTest\Attribute\AsRepository;

/**
 * @extends ServiceEntityRepository<IdcardValidationLog>
 */
#[AsRepository(entityClass: IdcardValidationLog::class)]
class IdcardValidationLogRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, IdcardValidationLog::class);
    }

    public function save(IdcardValidationLog $entity, bool $flush = true): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(IdcardValidationLog $entity, bool $flush = true): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * 根据身份证号码查找验证记录
     *
     * @return IdcardValidationLog[]
     */
    public function findByIdcardNumber(string $idcardNumber): array
    {
        /** @var IdcardValidationLog[] */
        return $this->createQueryBuilder('log')
            ->andWhere('log.idcardNumber = :idcardNumber')
            ->setParameter('idcardNumber', $idcardNumber)
            ->orderBy('log.createTime', 'DESC')
            ->addOrderBy('log.id', 'DESC')
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * 查找最近的验证记录
     *
     * @return IdcardValidationLog[]
     */
    public function findRecentValidations(int $limit = 20): array
    {
        /** @var IdcardValidationLog[] */
        return $this->createQueryBuilder('log')
            ->orderBy('log.createTime', 'DESC')
            ->addOrderBy('log.id', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * 根据用户查找验证记录
     *
     * @return IdcardValidationLog[]
     */
    public function findByUser(mixed $user): array
    {
        /** @var IdcardValidationLog[] */
        return $this->createQueryBuilder('log')
            ->andWhere('log.user = :user')
            ->setParameter('user', $user)
            ->orderBy('log.createTime', 'DESC')
            ->addOrderBy('log.id', 'DESC')
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * 获取验证统计信息
     *
     * @return array{valid: int, invalid: int, total: int}
     */
    public function getValidationStats(): array
    {
        $validCount = (int) $this->createQueryBuilder('log')
            ->select('COUNT(log.id)')
            ->andWhere('log.isValid = :valid')
            ->setParameter('valid', true)
            ->getQuery()
            ->getSingleScalarResult()
        ;

        $invalidCount = (int) $this->createQueryBuilder('log')
            ->select('COUNT(log.id)')
            ->andWhere('log.isValid = :invalid')
            ->setParameter('invalid', false)
            ->getQuery()
            ->getSingleScalarResult()
        ;

        $totalCount = (int) $this->createQueryBuilder('log')
            ->select('COUNT(log.id)')
            ->getQuery()
            ->getSingleScalarResult()
        ;

        return [
            'valid' => $validCount,
            'invalid' => $invalidCount,
            'total' => $totalCount,
        ];
    }
}
