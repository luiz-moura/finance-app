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
    public function __construct(
        private TransactionRepository $transactionRepository,
        private CategoryRepository $categoryRepository
    ) {}

    #[Route('/', name: 'app_transaction.index', methods: ['GET'])]
    public function index(): Response
    {
        $transactions = $this->transactionRepository->findAll();
        $balance = $this->transactionRepository->balance();
        $categories = $this->categoryRepository->findAll();

        return $this->render('transaction/index.html.twig', compact('transactions', 'categories', 'balance'));
    }

    #[Route('/transaction', name: 'app_transaction.store', methods: ['POST'])]
    public function store(Request $request, EntityManagerInterface $em, ValidatorInterface $validator): Response
    {
        $transaction = new Transaction();
        $transaction->setTitle($request->get('title'));
        $transaction->setValue($request->get('value'));
        $transaction->setType($request->get('type'));

        if (!empty($request->get('categories'))) {
            $categories = $this->categoryRepository->findBy(['id' => $request->get('categories')]);
            array_map(fn ($category) => $category->addTransaction($transaction), $categories);
        }

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
    public function edit(int $id): Response
    {
        $transaction = $this->transactionRepository->find($id);
        $categories = $this->categoryRepository->findAll();

        if (!$transaction) {
            throw $this->createNotFoundException('No transaction found for id ' . $id);
        }

        return $this->render('transaction/edit.html.twig', compact('transaction', 'categories'));
    }

    #[Route('/transaction/{id}', name: 'app_transaction.update', methods: ['PUT'])]
    public function update(Request $request, int $id, EntityManagerInterface $em, ValidatorInterface $validator): Response
    {
        $transaction = $this->transactionRepository->find($id);

        if (!$transaction) {
            throw $this->createNotFoundException('No transaction found for id ' . $id);
        }

        $transaction->setTitle($request->get('title'));
        $transaction->setValue($request->get('value'));
        $transaction->setType($request->get('type'));

        if (!empty($request->get('categories'))) {
            $myCats = $transaction->getCategories()->map(fn ($cat) => $cat->getId())->toArray();
            $catsRemoved = array_diff($myCats, $request->get('categories'));

            $categoriesAdd = $this->categoryRepository->findBy(['id' => $request->get('categories')]);
            $catsRemoved = $transaction->getCategories()->filter(fn ($cat) => in_array($cat->getId(), $catsRemoved))->toArray();

            array_map(fn ($cat) => $cat->addTransaction($transaction), $categoriesAdd);
            array_map(fn ($cat) => $cat->removeTransaction($transaction), $catsRemoved);
        } else {
            // Remove all categories
            array_map(fn ($cat) => $cat->removeTransaction($transaction), $transaction->getCategories()->toArray());
        }

        $errors = $validator->validate($transaction);

        if (count($errors) > 0) {
            return $this->render('transaction/edit.html.twig', compact('errors'));
        }

        $em->flush();

        $this->addFlash('success', 'Transaction updated with success');
        return $this->redirectToRoute('app_transaction.index');
    }

    #[Route('/transaction/{id}', name: 'app_transaction.delete', methods: ['DELETE'])]
    public function delete(int $id, EntityManagerInterface $em): Response
    {
        $transaction = $this->transactionRepository->find($id);

        if (!$transaction) {
            throw $this->createNotFoundException('No transaction found for id ' . $id);
        }

        $em->remove($transaction);
        $em->flush();

        $this->addFlash('success', 'Transaction deleted with success');
        return $this->redirectToRoute('app_transaction.index');
    }
}
