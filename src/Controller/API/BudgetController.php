<?php

namespace App\Controller\API;

use App\Entity\Budget;
use App\Repository\BudgetRepository;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class BudgetController extends AbstractController
{
    public function __construct(private BudgetRepository $budgetRepository) {}

    #[Route('api/budget', name: 'app_budget_api.index', methods: ['GET'])]
    public function index(): Response
    {
        $budgets = $this->budgetRepository->findAll();
        $budgetsArray = (new ArrayCollection($budgets))
            ->map(fn ($budget) => $budget->toArray())->toArray();

        return $this->json($budgetsArray);
    }

    #[Route('api/budget/{id}', name: 'app_budget_api.show', methods: ['GET'])]
    public function show(int $id): Response
    {
        $budget = $this->budgetRepository->find($id);
        return $this->json($budget->toArray());
    }

    #[Route('api/budget', name: 'app_budget_api.store', methods: ['POST'])]
    public function store(Request $request, CategoryRepository $categoryRepository, EntityManagerInterface $em, ValidatorInterface $validator): Response
    {
        $parameters = json_decode($request->getContent(), true);

        $budget = new Budget();
        $budget->setName($parameters['name']);
        $budget->setValue(tofloat($parameters['value']));

        $category = $categoryRepository->find($parameters['category_id']);
        $budget->setCategory($category);

        $errors = $validator->validate($budget);

        if (count($errors) > 0) {
            return $this->json(['error' => $errors], 400);
        }

        $em->persist($budget);
        $em->flush();

        return $this->json([
            'message' => 'Budget saved with success',
            'budget'  => $budget->toArray()
        ]);
    }

    #[Route('api/budget/{id}', name: 'app_budget_api.update', methods: ['PUT'])]
    public function update(Request $request, int $id, CategoryRepository $categoryRepository, EntityManagerInterface $em, ValidatorInterface $validator): Response
    {
        $budget = $this->budgetRepository->find($id);

        if (!$budget) {
            return $this->json(['error' => 'No budget found for id ' . $id], 404);
        }

        $parameters = json_decode($request->getContent(), true);

        $budget->setName($parameters['name']);
        $budget->setValue(tofloat($parameters['value']));

        $category = $categoryRepository->find($parameters['category_id']);
        $budget->setCategory($category);

        $errors = $validator->validate($budget);

        if (count($errors) > 0) {
            return $this->render('budget/edit.html.twig', compact('errors'));
        }

        $em->flush();

        return $this->json([
            'message' => 'Budget updated with success',
            'budget'  => $budget->toArray()
        ]);
    }

    #[Route('api/budget/{id}', name: 'app_budget_api.delete', methods: ['DELETE'])]
    public function delete(int $id, EntityManagerInterface $em): Response
    {
        $budget = $this->budgetRepository->find($id);

        if (!$budget) {
            return $this->json(['error' => 'No budget found for id ' . $id], 404);
        }

        $em->remove($budget);
        $em->flush();

        return $this->json('Successfully deleted', 204);
    }
}
