<?php

namespace App\Controller\API;

use App\Entity\Budget;
use App\Repository\BudgetRepository;
use App\Repository\CategoryRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class BudgetController extends AbstractController
{
    public function __construct(private BudgetRepository $budgetRepository) {}

    #[Route('api/budget', name: 'app_budget_api.index', methods: ['GET'])]
    public function index(): Response
    {
        $budgets = $this->budgetRepository->findAll();

        return $this->json($budgets, context: ['groups' => ['budget']]);
    }

    #[Route('api/budget/{id}', name: 'app_budget_api.show', methods: ['GET'])]
    public function show(int $id): Response
    {
        $budget = $this->budgetRepository->find($id);
        if (!$budget) {
            return $this->json(['message' => 'Budget not found.'], Response::HTTP_NOT_FOUND);
        }

        return $this->json($budget, context: ['groups' => ['budget']]);
    }

    #[Route('api/budget/category/{categoryId}', name: 'app_budget_api.store', methods: ['POST'])]
    public function store(
        int $categoryId,
        Request $request,
        CategoryRepository $categoryRepository,
        ValidatorInterface $validator
    ): Response
    {
        $category = $categoryRepository->find($categoryId);
        if (!$category) {
            return $this->json(['message' => 'Category not found.'], Response::HTTP_NOT_FOUND);
        }

        $body = json_decode($request->getContent(), true);

        $budget = new Budget();
        $budget->setName($body['name'] ?? null);
        $budget->setValue($body['value'] ?? null);
        $budget->setCategory($category);

        $errors = $validator->validate($budget);
        if ($errors->count() > 0) {
            foreach ($errors as $error) {
                $err[$error->getPropertyPath()][] = $error->getMessage();
            }

            return $this->json(['errors' => $err], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $this->budgetRepository->create($budget);

        return $this->json($budget, context: ['groups' => 'budget']);
    }

    #[Route('api/budget/{id}', name: 'app_budget_api.update', methods: ['PUT'])]
    public function update(
        int $id,
        Request $request,
        CategoryRepository $categoryRepository,
        ValidatorInterface $validator
    ): Response
    {
        $budget = $this->budgetRepository->find($id);
        if (!$budget) {
            return $this->json(['message' => 'Budget not found'], Response::HTTP_NOT_FOUND);
        }

        $body = json_decode($request->getContent(), true);

        $category = $categoryRepository->find($body['category_id']);
        if (!$category) {
            return $this->json(['message' => 'Category not found'], Response::HTTP_NOT_FOUND);
        }

        $budget->setName($body['name'] ?? null);
        $budget->setValue($body['value'] ?? null);
        $budget->setCategory($category);

        $errors = $validator->validate($budget);
        if ($errors->count() > 0) {
            foreach ($errors as $error) {
                $err[$error->getPropertyPath()][] = $error->getMessage();
            }

            return $this->json(['errors' => $err], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $this->budgetRepository->update($budget);

        return $this->json($budget, context: ['groups' => 'budget']);
    }

    #[Route('api/budget/{id}', name: 'app_budget_api.delete', methods: ['DELETE'])]
    public function delete(int $id): Response
    {
        $budget = $this->budgetRepository->find($id);
        if (!$budget) {
            return $this->json(['message' => 'Budget not found'], Response::HTTP_NOT_FOUND);
        }

        $this->budgetRepository->delete($budget);

        return $this->json([], status: Response::HTTP_NO_CONTENT);
    }
}
