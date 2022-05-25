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
    public function __construct(
        private TransactionRepository $transactionRepository,
        private CategoryRepository $categoryRepository
    ) {}

    #[Route('api/transaction/{transaction}/category/{category}', name: 'app_transaction-category_api.delete', methods: ['DELETE'])]
    public function delete(int $transaction, int $category, EntityManagerInterface $em): Response
    {
        $transaction = $this->transactionRepository->find($transaction);
        $category = $this->categoryRepository->find($category);

        if (!$category || !$transaction) {
            return $this->json(['error' => 'No category found'], 404);
        }

        $transaction->removeCategory($category);

        $em->flush();

        return $this->json(['message' => "Category {$category->getName()} removed"]);
    }
}
