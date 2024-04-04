<?php

namespace App\Controller\API;

use App\Repository\CategoryRepository;
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

    #[Route('api/transaction/{transactionId}/category/{categoryId}', name: 'app_transaction-category_api.delete', methods: ['DELETE'])]
    public function delete(int $transactionId, int $categoryId): Response
    {
        $transaction = $this->transactionRepository->find($transactionId);
        if (!$transaction) {
            return $this->json(['message' => 'Transaction not found.'], Response::HTTP_NOT_FOUND);
        }

        $category = $this->categoryRepository->find($categoryId);
        if (!$category) {
            return $this->json(['message' => 'Category not found.'], Response::HTTP_NOT_FOUND);
        }

        if (!$transaction->getCategories()->contains($category)) {
            return $this->json(['message' => 'Transaction does not have the category.'], Response::HTTP_NOT_FOUND);
        }

        $this->transactionRepository->detachCategory($transaction, $category);

        return $this->json([], Response::HTTP_NO_CONTENT);
    }
}
