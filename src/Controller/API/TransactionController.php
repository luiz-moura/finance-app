<?php

namespace App\Controller\API;

use App\Entity\Transaction;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\TransactionRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class TransactionController extends AbstractController
{
    #[Route('api/transaction', name: 'app_transaction_api.index', methods: ['GET'])]
    public function index(TransactionRepository $transactionRepository): Response
    {
        $transactions = new ArrayCollection($transactionRepository->findAll());
        $transactions = $transactions->map(fn ($transaction) => $transaction->toArray())->toArray();

        return $this->json($transactions);
    }

    #[Route('api/transaction/{id}', name: 'app_transaction_api.show', methods: ['GET'])]
    public function show(int $id, TransactionRepository $transactionRepository): Response
    {
        $transaction = $transactionRepository->find($id);
        return $this->json($transaction->toArray());
    }

    #[Route('api/transaction', name: 'app_transaction_api.store', methods: ['POST'])]
    public function store(
        Request $request,
        EntityManagerInterface $em,
        CategoryRepository $categoryRepository,
        ValidatorInterface $validator
    ): Response
    {
        $parameters = json_decode($request->getContent(), true);

        $transaction = new Transaction();
        $transaction->setTitle($parameters['title']);
        $transaction->setValue(tofloat($parameters['value']));
        $transaction->setType($parameters['type']);

        if (isset($parameters['categories']) && count($parameters['categories']) > 0) {
            $categories = $categoryRepository->findBy(['id' => $parameters['categories']]);
            array_map(fn ($category) => $category->addTransaction($transaction), $categories);
        }

        $errors = $validator->validate($transaction);

        if (count($errors) > 0) {
            return $this->json(['error' => $errors], 400);
        }

        $em->persist($transaction);
        $em->flush();

        return $this->json([
            'message'     => 'Successfully created',
            'transaction' => $transaction->toArray()
        ]);
    }

    #[Route('api/transaction/{id}', name: 'app_transaction_api.update', methods: ['PUT'])]
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
            return $this->json(['error' => 'No transaction found for id ' . $id], 404);
        }

        $parameters = json_decode($request->getContent(), true);

        $transaction->setTitle($parameters['title']);
        $transaction->setValue(tofloat($parameters['value']));
        $transaction->setType($parameters['type']);

        if (isset($parameters['categories']) && count($parameters['categories']) > 0) {
            $myCats = $transaction->getCategories()->map(fn ($cat) => $cat->getId())->toArray();
            $catsRemoved = array_diff($myCats, $parameters['categories']);

            $categoriesAdd = $categoryRepository->findBy(['id' => $parameters['categories']]);
            $catsRemoved = $transaction->getCategories()->filter(fn ($cat) => in_array($cat->getId(), $catsRemoved))->toArray();

            array_map(fn ($cat) => $cat->addTransaction($transaction), $categoriesAdd);
            array_map(fn ($cat) => $cat->removeTransaction($transaction), $catsRemoved);
        } else {
            // Remove all categories
            array_map(fn ($cat) => $cat->removeTransaction($transaction), $transaction->getCategories()->toArray());
        }

        $errors = $validator->validate($transaction);

        if (count($errors) > 0) {
            $this->json(['error' => $errors], 400);
        }

        $em->flush();

        return $this->json([
            'message'     => 'Successfully updated',
            'transaction' => $transaction->toArray()
        ]);
    }

    #[Route('api/transaction/{id}', name: 'app_transaction_api.delete', methods: ['DELETE'])]
    public function delete(int $id, EntityManagerInterface $em, TransactionRepository $transactionRepository): Response
    {
        $transaction = $transactionRepository->find($id);

        if (!$transaction) {
            return $this->json(['error' => 'No transaction found for id ' . $id], 404);
        }

        $em->remove($transaction);
        $em->flush();

        return $this->json('Successfully deleted', 204);
    }
}
