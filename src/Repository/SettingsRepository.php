<?php

namespace CreativePoint\SettingsBundle\Repository;

use CreativePoint\SettingsBundle\Entity\SettingsInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<SettingsInterface>
 *
 * @method SettingsInterface|null find($id, $lockMode = null, $lockVersion = null)
 * @method SettingsInterface|null findOneBy(array $criteria, array $orderBy = null)
 * @method SettingsInterface[]    findAll()
 * @method SettingsInterface[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SettingsRepository extends ServiceEntityRepository implements SettingsRepositoryInterface
{
    public function __construct(ManagerRegistry $registry, string $entityClass)
    {
        parent::__construct($registry, $entityClass);
    }

    public function add(SettingsInterface $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(SettingsInterface $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
