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
    #[Route('/transaction/{transaction}/category/{category}', name: 'app_transaction-category.delete', methods: ['DELETE'])]
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
            throw $this->createNotFoundException('No category found');
        }

        $transaction->removeCategory($category);

        $em->flush();

        $this->addFlash('success', "Category {$category->getName()} removed");
        return $this->redirectToRoute('app_transaction.index');
    }
}
