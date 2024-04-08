<?php

namespace App\Repository;

use App\Entity\Category;
use App\Entity\Transaction;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Persistence\ManagerRegistry;

class TransactionRepository extends ServiceEntityRepository
{
    public function __construct(
        ManagerRegistry $registry,
        private CategoryRepository $categoryRepository,
    ) {
        parent::__construct($registry, Transaction::class);
    }

    public function create(Transaction $transaction): void
    {
        $this->getEntityManager()->persist($transaction);
        $this->getEntityManager()->flush();
    }

    public function update(Transaction $transaction): void
    {
        $this->getEntityManager()->persist($transaction);
        $this->getEntityManager()->flush();
    }

    public function delete(Transaction $transaction): void
    {
        $this->getEntityManager()->remove($transaction);
        $this->getEntityManager()->flush();
    }

    public function detachCategory(Transaction $transaction, Category $category): void
    {
        $transaction->removeCategory($category);
        $this->getEntityManager()->flush();
    }

    public function syncCategories(Transaction $transaction, array $categoriesIds)
    {
        if (!$categoriesIds) {
            $transaction->getCategories()->map(fn ($cat) => $transaction->removeCategory($cat));
        }

        $categoriesCurrentIds = $transaction->getCategories()
            ->map(fn ($cat) => $cat->getId())
            ->toArray();

        $categoriesIdsRemoved = array_diff($categoriesCurrentIds, $categoriesIds);
        $transaction->getCategories()
            ->filter(fn ($cat) => in_array($cat->getId(), $categoriesIdsRemoved))
            ->map(fn ($cat) => $transaction->removeCategory($cat));

        $categoriesIdsAdded = array_diff($categoriesIds, $categoriesCurrentIds);
        (new ArrayCollection($this->categoryRepository->findBy(['id' => $categoriesIdsAdded])))
            ->map(fn ($cat) => $transaction->addCategory($cat));
    }

    public function findWithCategories($id): ?Transaction
    {
        return $this->createQueryBuilder('t')
            ->leftJoin('t.categories', 'c')
            ->addSelect('c')
            ->andWhere('t.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function getAccountBalance(): string
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
}
