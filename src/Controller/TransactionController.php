<?php

namespace App\Controller;

use App\Entity\Transaction;
use App\Repository\CategoryRepository;
use App\Repository\TransactionRepository;
use App\Services\CurrencyService;
use Doctrine\Common\Collections\Collection;
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
    public function index(CurrencyService $currencyService): Response
    {
        $transactions = $this->transactionRepository->findAll();
        $categories = $this->categoryRepository->findAll();
        $balance = $this->transactionRepository->getAccountBalance();
        $coins = $currencyService->getCoinsWithExchangeValue();

        return $this->render('transaction/index.html.twig', compact(
            'transactions',
            'categories',
            'balance',
            'coins'
        ));
    }

    #[Route('/transaction', name: 'app_transaction.store', methods: ['POST'])]
    public function store(Request $request, ValidatorInterface $validator): Response
    {
        $transaction = new Transaction();
        $transaction->setTitle($request->get('title'));
        $transaction->setValue($request->get('value'));
        $transaction->setType($request->get('type'));

        if ($request->get('categories')) {
            $categories = $this->categoryRepository->findBy(['id' => $request->get('categories')]);
            array_map(fn ($category) => $transaction->addCategory($category), $categories);
        }

        $errors = $validator->validate($transaction);
        if ($errors->count() > 0) {
            return $this->redirectToRoute('app_transaction.index', compact('errors'));
        }

        $this->transactionRepository->create($transaction);

        $this->addFlash('success', 'Transaction saved with success');

        return $this->redirectToRoute('app_transaction.index');
    }

    #[Route('transaction/{id}', name: 'app_transaction.edit', methods: ['GET'])]
    public function edit(int $id): Response
    {
        $transaction = $this->transactionRepository->findWithCategories($id);
        if (!$transaction) {
            throw $this->createNotFoundException('Transaction not found');
        }

        $categories = $this->categoryRepository->findAll();

        return $this->render('transaction/edit.html.twig', compact('transaction', 'categories'));
    }

    #[Route('/transaction/{id}', name: 'app_transaction.update', methods: ['PUT'])]
    public function update(Request $request, int $id, ValidatorInterface $validator): Response
    {
        $transaction = $this->transactionRepository->find($id);
        if (!$transaction) {
            throw $this->createNotFoundException('Transaction not found');
        }

        $transaction->setTitle($request->get('title'));
        $transaction->setValue($request->get('value'));
        $transaction->setType($request->get('type'));

        if ($request->get('categories')) {
            $transactionCategories = $transaction->getCategories()
                ->map(fn ($cat) => $cat->getId())
                ->toArray();

            $categoriesIdsRemoved = array_diff($transactionCategories, $request->get('categories'));
            $transaction->getCategories()
                ->filter(fn ($cat) => in_array($cat->getId(), $categoriesIdsRemoved))
                ->map(fn ($cat) => $transaction->removeCategory($cat));

            $categoriesIdsAdded = array_diff($request->get('categories'), $transactionCategories);
            $categories = $this->categoryRepository->findBy(['id' => $categoriesIdsAdded]);
            array_map(fn ($cat) => $transaction->addCategory($cat), $categories);
        } else {
            $transaction->getCategories()->map(fn ($cat) => $transaction->removeCategory($cat));
        }

        $errors = $validator->validate($transaction);
        if ($errors->count() > 0) {
            return $this->redirectToRoute('app_transaction.index', compact('errors'));
        }

        $this->transactionRepository->update($transaction);

        $this->addFlash('success', 'Transaction updated with success');

        return $this->redirectToRoute('app_transaction.index');
    }

    #[Route('/transaction/{id}', name: 'app_transaction.delete', methods: ['DELETE'])]
    public function delete(int $id): Response
    {
        $transaction = $this->transactionRepository->find($id);
        if (!$transaction) {
            throw $this->createNotFoundException('Transaction not found');
        }

        $this->transactionRepository->delete($transaction);

        $this->addFlash('success', 'Transaction deleted with success');

        return $this->redirectToRoute('app_transaction.index');
    }
}
