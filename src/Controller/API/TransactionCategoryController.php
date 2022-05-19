<?php

namespace App\Controller\API;

use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\TransactionRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class TransactionCategoryController extends AbstractController
{
    #[Route('api/transaction/{transaction}/category/{category}', name: 'app_transaction-category.delete', methods: ['DELETE'])]
    public function delete(
        int $transaction,
        int $category,
        TransactionRepository $transactionRepository,
        CategoryRepository $categoryRepository,
        EntityManagerInterface $em
    ): Response
    {
        $transaction = $transactionRepository->find($transaction);
        $category = $categoryRepository->find($category);

        if (!$category || !$transaction) {
            return $this->json(['error' => 'No category found'], 404);
        }

        $transaction->removeCategory($category);

        $em->flush();

        return $this->json(['message' => "Category {$category->getName()} removed"]);
    }
}
