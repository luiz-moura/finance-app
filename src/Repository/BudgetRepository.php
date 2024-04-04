<?php

namespace App\Repository;

use App\Entity\Budget;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class BudgetRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Budget::class);
    }

    public function create(Budget $budget): void
    {
        $this->getEntityManager()->persist($budget);
        $this->getEntityManager()->flush();
    }

    public function update(Budget $budget): void
    {
        $this->getEntityManager()->persist($budget);
        $this->getEntityManager()->flush();
    }

    public function delete(Budget $budget): void
    {
        $this->getEntityManager()->remove($budget);
        $this->getEntityManager()->flush();
    }
}
