<?php

namespace App\Controller;

use App\Entity\Budget;
use App\Repository\BudgetRepository;
use Doctrine\ORM\EntityManagerInterface;
use Entity\Repository\CategoryRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class BudgetController extends AbstractController
{
    public function __construct(private BudgetRepository $budgetRepository) {}

    #[Route('/budget', name: 'app_budget.index', methods: ['GET'])]
    public function index(): Response
    {
        $budgets = $this->budgetRepository->findAll();

        return $this->render('budget/index.html.twig', compact('budgets'));
    }

    #[Route('budget/create', name: 'app_budget.create', methods: ['GET'])]
    public function create(CategoryRepository $categoryRepository): Response
    {
        $categories = $categoryRepository->findAll();
        return $this->render('budget/create.html.twig', compact('categories'));
    }

    #[Route('/budget', name: 'app_budget.store', methods: ['POST'])]
    public function store(Request $request, CategoryRepository $categoryRepository, EntityManagerInterface $em, ValidatorInterface $validator): Response
    {
        $budget = new Budget();
        $budget->setName($request->get('name'));
        $budget->setValue($request->get('value'));

        $category = $categoryRepository->find($request->get('category_id'));
        $budget->setCategory($category);

        $errors = $validator->validate($budget);

        if (count($errors) > 0) {
            return $this->render('budget/create.html.twig', compact('errors'));
        }

        $em->persist($budget);
        $em->flush();

        $this->addFlash('success', 'Budget saved with success');
        return $this->redirectToRoute('app_budget.index');
    }

    #[Route('budget/{id}', name: 'app_budget.edit', methods: ['GET'])]
    public function edit(int $id, CategoryRepository $categoryRepository): Response
    {
        $budget = $this->budgetRepository->find($id);
        $categories = $categoryRepository->findAll();

        if (!$budget) {
            throw $this->createNotFoundException('No budget found for id ' . $id);
        }

        return $this->render('budget/edit.html.twig', compact('budget', 'categories'));
    }

    #[Route('/budget/{id}', name: 'app_budget.update', methods: ['PUT'])]
    public function update(Request $request, int $id, CategoryRepository $categoryRepository, EntityManagerInterface $em, ValidatorInterface $validator): Response
    {
        $budget = $this->budgetRepository->find($id);

        if (!$budget) {
            throw $this->createNotFoundException('No budget found for id ' . $id);
        }

        $budget->setName($request->get('name'));
        $budget->setValue($request->get('value'));

        $category = $categoryRepository->find($request->get('category_id'));
        $budget->setCategory($category);

        $errors = $validator->validate($budget);

        if (count($errors) > 0) {
            return $this->render('budget/edit.html.twig', compact('errors'));
        }

        $em->flush();

        $this->addFlash('success', 'Budget updated with success');
        return $this->redirectToRoute('app_budget.index');
    }

    #[Route('/budget/{id}', name: 'app_budget.delete', methods: ['DELETE'])]
    public function delete(int $id, EntityManagerInterface $em): Response
    {
        $budget = $this->budgetRepository->find($id);

        if (!$budget) {
            throw $this->createNotFoundException('No budget found for id ' . $id);
        }

        $em->remove($budget);
        $em->flush();

        $this->addFlash('success', 'Budget deleted with success');
        return $this->redirectToRoute('app_budget.index');
    }
}
