<?php

namespace App\Controller;

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

    #[Route('/transaction/{transaction}/category/{category}', name: 'app_transaction-category.delete', methods: ['DELETE'])]
    public function delete(int $transaction, int $category, EntityManagerInterface $em): Response
    {
        $transaction = $this->transactionRepository->find($transaction);
        $category = $this->categoryRepository->find($category);

        if (!$category || !$transaction) {
            throw $this->createNotFoundException('No category found');
        }

        $transaction->removeCategory($category);

        $em->flush();

        $this->addFlash('success', "Category {$category->getName()} removed");
        return $this->redirectToRoute('app_transaction.index');
    }
}
