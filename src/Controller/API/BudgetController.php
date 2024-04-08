<?php

namespace App\Controller\API;

use App\Entity\Budget;
use App\Repository\BudgetRepository;
use App\Repository\CategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
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

    #[Route('api/budget', name: 'app_budget_api.store', methods: ['POST'])]
    public function store(
        Request $request,
        CategoryRepository $categoryRepository,
        ValidatorInterface $validator
    ): Response
    {
        $body = new ArrayCollection(json_decode($request->getContent(), true));

        $category = $body->get('category_id')
            ? $categoryRepository->find($body->get('category_id'))
            : null;

        $budget = new Budget();
        $budget->setName($body->get('name'))
            ->setValue($body->get('value'))
            ->setCategory($category);

        $violations = $validator->validate($budget);
        if ($violations->count()) {
            return $this->json(['errors' => createErrorPayload($violations)], Response::HTTP_UNPROCESSABLE_ENTITY);
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

        $body = new ArrayCollection(json_decode($request->getContent(), true));

        $category = $body->get('category_id')
            ? $categoryRepository->find($body->get('category_id'))
            : null;

        $budget->setName($body->get('name'))
            ->setValue($body->get('value'))
            ->setCategory($category);

        $violations = $validator->validate($budget);
        if ($violations->count() > 0) {
            return $this->json(['errors' => createErrorPayload($violations)], Response::HTTP_UNPROCESSABLE_ENTITY);
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
