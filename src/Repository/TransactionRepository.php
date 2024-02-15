<?php

namespace App\Repository;

use App\Entity\Transaction;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class TransactionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Transaction::class);
    }

    public function add(Transaction $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Transaction $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function balance(): string
    {
        $withdraw = $this->createQueryBuilder('t')
            ->select('SUM(t.value) AS total')
            ->andWhere('t.type = :val')
            ->setParameter('val', 'withdraw')
            ->getQuery()
            ->getOneOrNullResult()['total'];

        $deposit = $this->createQueryBuilder('t')
            ->select('SUM(t.value) AS total')
            ->andWhere('t.type = :val')
            ->setParameter('val', 'deposit')
            ->getQuery()
            ->getOneOrNullResult()['total'];

        $total = $deposit - $withdraw;

        return number_format($total, 2, ',', '.');
    }

    public function findAllGreaterThanPrice(): array
    {
        $qb = $this->createQueryBuilder('t')
            ->orderBy('t.created_at', 'ASC');

        $query = $qb->getQuery();

        return $query->execute();
    }
}
