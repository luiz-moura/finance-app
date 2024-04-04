<?php

namespace App\Controller;

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
    public function __construct(
        private BudgetRepository $budgetRepository,
        private CategoryRepository $categoryRepository
    ) {}

    #[Route('/budget', name: 'app_budget.index', methods: ['GET'])]
    public function index(): Response
    {
        $budgets = $this->budgetRepository->findAll();
        $categories = $this->categoryRepository->findAll();

        return $this->render('budget/index.html.twig', compact('budgets', 'categories'));
    }

    #[Route('budget/create', name: 'app_budget.create', methods: ['GET'])]
    public function create(): Response
    {
        $categories = $this->categoryRepository->findAll();

        return $this->render('budget/create.html.twig', compact('categories'));
    }

    #[Route('/budget', name: 'app_budget.store', methods: ['POST'])]
    public function store(Request $request, ValidatorInterface $validator): Response
    {
        $category = $this->categoryRepository->find($request->get('category_id'));
        if (!$category) {
            throw $this->createNotFoundException('Category not found');
        }

        $budget = new Budget();
        $budget->setName($request->get('name'));
        $budget->setValue($request->get('value'));
        $budget->setCategory($category);

        $errors = $validator->validate($budget);
        if ($errors->count() > 0) {
            return $this->redirectToRoute('app_budget.index', compact('errors'));
        }

        $this->budgetRepository->create($budget);

        $this->addFlash('success', 'Budget saved with success');

        return $this->redirectToRoute('app_budget.index');
    }

    #[Route('budget/{id}', name: 'app_budget.edit', methods: ['GET'])]
    public function edit(int $id): Response
    {
        $budget = $this->budgetRepository->find($id);
        if (!$budget) {
            throw $this->createNotFoundException('Budget not found');
        }

        $categories = $this->categoryRepository->findAll();

        return $this->render('budget/edit.html.twig', compact('budget', 'categories'));
    }

    #[Route('/budget/{id}', name: 'app_budget.update', methods: ['PUT'])]
    public function update(int $id, Request $request, ValidatorInterface $validator): Response
    {
        $budget = $this->budgetRepository->find($id);
        if (!$budget) {
            throw $this->createNotFoundException('Budget not found');
        }

        $category = $this->categoryRepository->find($request->get('category_id'));
        if (!$category) {
            throw $this->createNotFoundException('Category not found');
        }

        $budget->setName($request->get('name'));
        $budget->setValue($request->get('value'));
        $budget->setCategory($category);

        $errors = $validator->validate($budget);
        if ($errors->count() > 0) {
            return $this->redirectToRoute('app_budget.index', compact('errors'));
        }

        $this->budgetRepository->update($budget);

        $this->addFlash('success', 'Budget updated with success');

        return $this->redirectToRoute('app_budget.index');
    }

    #[Route('/budget/{id}', name: 'app_budget.delete', methods: ['DELETE'])]
    public function delete(int $id): Response
    {
        $budget = $this->budgetRepository->find($id);
        if (!$budget) {
            throw $this->createNotFoundException('Budget not found');
        }

        $this->budgetRepository->delete($budget);

        $this->addFlash('success', 'Budget deleted with success');

        return $this->redirectToRoute('app_budget.index');
    }
}
