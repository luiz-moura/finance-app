<?php

namespace App\Controller;

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

    #[Route('/transaction/{transactionId}/category/{categoryId}', name: 'app_transaction-category.delete', methods: ['DELETE'])]
    public function delete(int $transactionId, int $categoryId): Response
    {
        $transaction = $this->transactionRepository->find($transactionId);
        if (!$transaction) {
            throw $this->createNotFoundException('Transaction not found');
        }

        $category = $this->categoryRepository->find($categoryId);
        if (!$category) {
            throw $this->createNotFoundException('Category not found');
        }

        $this->transactionRepository->detachCategory($transaction, $category);

        $this->addFlash('success', "Category {$category->getName()} removed");

        return $this->redirectToRoute('app_transaction.index');
    }
}
