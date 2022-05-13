<?php

namespace App\Controller;

use App\Entity\Category;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CategoryController extends AbstractController
{
    #[Route('/category', name: 'app_category.index', methods: ['GET'])]
    public function index(CategoryRepository $categoryRepository): Response
    {
        $categories = $categoryRepository->findAll();

        return $this->render('category/index.html.twig', compact('categories'));
    }

    #[Route('category/create', name: 'app_category.create', methods: ['GET'])]
    public function create(): Response
    {
        return $this->render('category/create.html.twig');
    }

    #[Route('/category', name: 'app_category.store', methods: ['POST'])]
    public function store(Request $request, EntityManagerInterface $em, ValidatorInterface $validator): Response
    {
        $category = new Category();
        $category->setName($request->get('name'));
        $category->setBackground($request->get('background'));

        $errors = $validator->validate($category);

        if (count($errors) > 0) {
            return $this->render('category/create.html.twig', compact('errors'));
        }

        $em->persist($category);
        $em->flush();

        $this->addFlash('success', 'Category saved with success');
        return $this->redirectToRoute('app_category.index');
    }

    #[Route('category/{id}', name: 'app_category.edit', methods: ['GET'])]
    public function edit(int $id, CategoryRepository $categoryRepository): Response
    {
        $category = $categoryRepository->find($id);

        if (!$category) {
            throw $this->createNotFoundException('No category found for id ' . $id);
        }

        return $this->render('category/edit.html.twig', compact('category'));
    }

    #[Route('/category/{id}', name: 'app_category.update', methods: ['PUT'])]
    public function update(
        Request $request,
        int $id,
        CategoryRepository $categoryRepository,
        EntityManagerInterface $em,
        ValidatorInterface $validator
    ): Response
    {
        $category = $categoryRepository->find($id);

        if (!$category) {
            throw $this->createNotFoundException('No category found for id ' . $id);
        }

        $category->setName($request->get('name'));
        $category->setBackground($request->get('background'));

        $errors = $validator->validate($category);

        if (count($errors) > 0) {
            return $this->render('category/edit.html.twig', compact('errors'));
        }

        $em->flush();

        $this->addFlash('success', 'Category updated with success');
        return $this->redirectToRoute('app_category.index');
    }

    #[Route('/category/{id}', name: 'app_category.delete', methods: ['DELETE'])]
    public function delete(int $id, EntityManagerInterface $em, CategoryRepository $categoryRepository): Response
    {
        $category = $categoryRepository->find($id);

        if (!$category) {
            throw $this->createNotFoundException('No category found for id ' . $id);
        }

        $em->remove($category);
        $em->flush();

        $this->addFlash('success', 'Category deleted with success');
        return $this->redirectToRoute('app_category.index');
    }
}
