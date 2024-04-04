<?php

namespace App\Controller\API;

use App\Entity\Transaction;
use App\Services\FileService;
use App\Repository\CategoryRepository;
use App\Repository\TransactionRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class TransactionController extends AbstractController
{
    public function __construct(private TransactionRepository $transactionRepository) {}

    #[Route('api/transaction', name: 'app_transaction_api.index', methods: ['GET'])]
    public function index(): Response
    {
        $transactions = $this->transactionRepository->findAll();

        return $this->json($transactions, context: ['groups' => 'transaction']);
    }

    #[Route('api/transaction/{id}', name: 'app_transaction_api.show', methods: ['GET'])]
    public function show(int $id): Response
    {
        $transaction = $this->transactionRepository->find($id);
        if (!$transaction) {
            return $this->json(['message' => 'Transaction not found.'], Response::HTTP_NOT_FOUND);
        }

        return $this->json($transaction, context: ['groups' => 'transaction']);
    }

    #[Route('api/transaction', name: 'app_transaction_api.store', methods: ['POST'])]
    public function store(
        Request $request,
        CategoryRepository $categoryRepository,
        FileService $fileUploader,
        ValidatorInterface $validator
    ): Response {
        $body = json_decode($request->getContent(), true);

        $transaction = new Transaction();
        $transaction->setTitle($body['title'] ?? null);
        $transaction->setValue($body['value'] ?? null);
        $transaction->setType($body['type'] ?? null);

        if (isset($body['categories'])) {
            $categories = $categoryRepository->findBy(['id' => $body['categories']]);
            array_map(fn ($category) => $category->addTransaction($transaction), $categories);
        }

        if ($request->files->get('image')) {
            $path = $fileUploader->store($request->files->get('image'));
            $transaction->setImage($path);
        }

        $errors = $validator->validate($transaction);
        if ($errors->count() > 0) {
            foreach ($errors as $error) {
                $err[$error->getPropertyPath()][] = $error->getMessage();
            }

            return $this->json(['errors' => $err], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $this->transactionRepository->create($transaction);

        return $this->json($transaction, context: ['groups' => 'transaction']);
    }

    #[Route('api/transaction/{id}', name: 'app_transaction_api.update', methods: ['PUT', 'POST'])]
    public function update(
        int $id,
        Request $request,
        CategoryRepository $categoryRepository,
        FileService $fileUploader,
        ValidatorInterface $validator
    ): Response {
        $transaction = $this->transactionRepository->find($id);
        if (!$transaction) {
            return $this->json(['message' => 'Transaction not found.'], Response::HTTP_NOT_FOUND);
        }

        $body = json_decode($request->getContent(), true);

        $transaction->setTitle($body['title'] ?? null);
        $transaction->setValue($body['value'] ?? null);
        $transaction->setType($body['type'] ?? null);

        if (isset($body['categories'])) {
            $transactionCategories = $transaction->getCategories()
                ->map(fn ($cat) => $cat->getId())
                ->toArray();

            $categoriesIdsRemoved = array_diff($transactionCategories, $body['categories']);
            $transaction->getCategories()
                ->filter(fn ($cat) => in_array($cat->getId(), $categoriesIdsRemoved))
                ->map(fn ($cat) => $transaction->removeCategory($cat));

            $categoriesIdsAdded = array_diff($body['categories'], $transactionCategories);
            $categories = $categoryRepository->findBy(['id' => $categoriesIdsAdded]);
            array_map(fn ($cat) => $transaction->addCategory($cat), $categories);
        } else {
            $transaction->getCategories()->map(fn ($cat) => $transaction->removeCategory($cat));
        }

        $errors = $validator->validate($transaction);
        if ($errors->count() > 0) {
            foreach ($errors as $error) {
                $err[$error->getPropertyPath()][] = $error->getMessage();
            }

            return $this->json(['errors' => $err], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        if ($request->files->get('image')) {
            $fileUploader->remove($transaction->getImageDir());
            $path = $fileUploader->store($request->files->get('image'));
            $transaction->setImage($path);
        }

        $this->transactionRepository->update($transaction);

        return $this->json($transaction, context: ['groups' => 'transaction']);
    }

    #[Route('api/transaction/{id}', name: 'app_transaction_api.delete', methods: ['DELETE'])]
    public function delete(int $id, FileService $fileUploader): Response
    {
        $transaction = $this->transactionRepository->find($id);
        if (!$transaction) {
            return $this->json(['message' => 'Transaction not found.'], Response::HTTP_NOT_FOUND);
        }

        if ($transaction->getImage()) {
            $fileUploader->remove($transaction->getImageDir());
        }

        $this->transactionRepository->delete($transaction);

        return $this->json([], Response::HTTP_NO_CONTENT);
    }
}
