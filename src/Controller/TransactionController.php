<?php

namespace App\Controller;

use App\Entity\Transaction;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\TransactionRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class TransactionController extends AbstractController
{
    #[Route('/', name: 'app_transaction.index', methods: ['GET'])]
    public function index(TransactionRepository $transactionRepository, CategoryRepository $categoryRepository): Response
    {
        $transactions = $transactionRepository->findAll();
        $balance = $transactionRepository->balance();
        $categories = $categoryRepository->findAll();

        return $this->render('transaction/index.html.twig', compact('transactions', 'categories', 'balance'));
    }

    #[Route('/transaction', name: 'app_transaction.store', methods: ['POST'])]
    public function store(
        Request $request,
        EntityManagerInterface $em,
        CategoryRepository $categoryRepository,
        ValidatorInterface $validator
    ): Response
    {
        $transaction = new Transaction();
        $transaction->setTitle($request->get('title'));
        $transaction->setValue($request->get('value'));
        $transaction->setType($request->get('type'));

        $categories = $categoryRepository->findBy(['id' => $request->get('categories')]);
        array_map(fn ($category) => $category->addTransaction($transaction), $categories);

        $errors = $validator->validate($transaction);

        if (count($errors) > 0) {
            return $this->redirectToRoute('app_transaction.index');
        }

        $em->persist($transaction);
        $em->flush();

        $this->addFlash('success', 'Transaction saved with success');
        return $this->redirectToRoute('app_transaction.index');
    }

    #[Route('transaction/{id}', name: 'app_transaction.edit', methods: ['GET'])]
    public function edit(int $id, TransactionRepository $transactionRepository, CategoryRepository $categoryRepository): Response
    {
        $transaction = $transactionRepository->find($id);
        $categories = $categoryRepository->findAll();

        if (!$transaction) {
            throw $this->createNotFoundException('No transaction found for id ' . $id);
        }

        return $this->render('transaction/edit.html.twig', compact('transaction', 'categories'));
    }

    #[Route('/transaction/{id}', name: 'app_transaction.update', methods: ['PUT'])]
    public function update(
        Request $request,
        int $id,
        TransactionRepository $transactionRepository,
        CategoryRepository $categoryRepository,
        EntityManagerInterface $em,
        ValidatorInterface $validator
    ): Response
    {
        $transaction = $transactionRepository->find($id);

        if (!$transaction) {
            throw $this->createNotFoundException('No transaction found for id ' . $id);
        }

        $transaction->setTitle($request->get('title'));
        $transaction->setValue($request->get('value'));
        $transaction->setType($request->get('type'));

        $categories = $categoryRepository->findBy(['id' => $request->get('categories')]);
        array_map(fn ($category) => $category->addTransaction($transaction), $categories);

        $errors = $validator->validate($transaction);

        if (count($errors) > 0) {
            return $this->render('transaction/edit.html.twig', compact('errors'));
        }

        $em->flush();

        $this->addFlash('success', 'Transaction updated with success');
        return $this->redirectToRoute('app_transaction.index');
    }

    #[Route('/transaction/{id}', name: 'app_transaction.delete', methods: ['DELETE'])]
    public function delete(int $id, EntityManagerInterface $em, TransactionRepository $transactionRepository): Response
    {
        $transaction = $transactionRepository->find($id);

        if (!$transaction) {
            throw $this->createNotFoundException('No transaction found for id ' . $id);
        }

        $em->remove($transaction);
        $em->flush();

        $this->addFlash('success', 'Transaction deleted with success');
        return $this->redirectToRoute('app_transaction.index');
    }
}
