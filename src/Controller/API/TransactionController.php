<?php

namespace App\Controller\API;

use App\Entity\Transaction;
use App\Services\FileService;
use App\Repository\CategoryRepository;
use App\Repository\TransactionRepository;
use Doctrine\Common\Collections\ArrayCollection;
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
        FileService $fileService,
        ValidatorInterface $validator
    ): Response {
        $body = new ArrayCollection(json_decode($request->getContent(), true));

        $transaction = new Transaction();
        $transaction->setTitle($body->get('title'))
            ->setValue($body->get('value'))
            ->setType($body->get('type'));

        if ($body->containsKey('categories')) {
            $categories = $categoryRepository->findBy(['id' => $body->get('categories')]);
            array_map(fn ($category) => $transaction->addCategory($category), $categories);
        }

        $violations = $validator->validate($transaction);
        if ($violations->count() > 0) {
            return $this->json(['errors' => createErrorPayload($violations)], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        if ($request->files->get('image')) {
            $path = $fileService->store($request->files->get('image'));
            $transaction->setImage($path);
        }

        $this->transactionRepository->create($transaction);

        return $this->json($transaction, context: ['groups' => 'transaction']);
    }

    #[Route('api/transaction/{id}', name: 'app_transaction_api.update', methods: ['PUT', 'POST'])]
    public function update(
        int $id,
        Request $request,
        CategoryRepository $categoryRepository,
        FileService $fileService,
        ValidatorInterface $validator
    ): Response {
        $transaction = $this->transactionRepository->find($id);
        if (!$transaction) {
            return $this->json(['message' => 'Transaction not found.'], Response::HTTP_NOT_FOUND);
        }

        $body = new ArrayCollection(json_decode($request->getContent(), true));

        $transaction->setTitle($body->get('title'))
            ->setValue($body->get('value'))
            ->setType($body->get('type'));

        if ($body->containsKey('categories')) {
            $this->transactionRepository->syncCategories($transaction, $body->get('categories'));
        }

        if ($request->files->get('image')) {
            $fileService->remove($transaction->getImageDir());
            $path = $fileService->store($request->files->get('image'));
            $transaction->setImage($path);
        }

        $violations = $validator->validate($transaction);
        if ($violations->count() > 0) {
            return $this->json(['errors' => createErrorPayload($violations)], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $this->transactionRepository->update($transaction);

        return $this->json($transaction, context: ['groups' => 'transaction']);
    }

    #[Route('api/transaction/{id}', name: 'app_transaction_api.delete', methods: ['DELETE'])]
    public function delete(int $id, FileService $fileService): Response
    {
        $transaction = $this->transactionRepository->find($id);
        if (!$transaction) {
            return $this->json(['message' => 'Transaction not found.'], Response::HTTP_NOT_FOUND);
        }

        if ($transaction->getImage()) {
            $fileService->remove($transaction->getImageDir());
        }

        $this->transactionRepository->delete($transaction);

        return $this->json([], Response::HTTP_NO_CONTENT);
    }
}
