<?php

namespace App\Controller\API;

use App\Entity\Category;
use App\Repository\CategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CategoryController extends AbstractController
{
    public function __construct(private CategoryRepository $categoryRepository) {}

    #[Route('api/category', name: 'app_category_api.index', methods: ['GET'])]
    public function index(): Response
    {
        $categories = $this->categoryRepository->findAll();

        return $this->json($categories, context: ['groups' => 'category']);
    }

    #[Route('api/category/{id}', name: 'app_category_api.show', methods: ['GET'])]
    public function show(int $id): Response
    {
        $category = $this->categoryRepository->find($id);
        if (!$category) {
            return $this->json(['message' => 'Category not found.'], Response::HTTP_NOT_FOUND);
        }

        return $this->json($category, context: ['groups' => 'category']);
    }

    #[Route('api/category', name: 'app_category_api.store', methods: ['POST'])]
    public function store(Request $request, ValidatorInterface $validator): Response
    {
        $body = new ArrayCollection(json_decode($request->getContent(), true));

        $category = new Category();
        $category->setName($body->get('name'))
            ->setBackground($body->get('background'));

        $violations = $validator->validate($category);
        if ($violations->count() > 0) {
            return $this->json(['errors' => createErrorPayload($violations)], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $this->categoryRepository->create($category);

        return $this->json($category, context: ['groups' => 'category']);
    }

    #[Route('api/category/{id}', name: 'app_category_api.update', methods: ['PUT'])]
    public function update(int $id, Request $request, ValidatorInterface $validator): Response
    {
        $category = $this->categoryRepository->find($id);
        if (!$category) {
            return $this->json(['message' => 'Category not found.'], Response::HTTP_NOT_FOUND);
        }

        $body = new ArrayCollection(json_decode($request->getContent(), true));

        $category->setName($body->get('name'))
            ->setBackground($body->get('background'));

        $violations = $validator->validate($category);
        if ($violations->count() > 0) {
            return $this->json(['errors' => createErrorPayload($violations)], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $this->categoryRepository->update($category);

        return $this->json($category, context: ['groups' => 'category']);
    }

    #[Route('api/category/{id}', name: 'app_category_api.delete', methods: ['DELETE'])]
    public function delete(int $id): Response
    {
        $category = $this->categoryRepository->find($id);
        if (!$category) {
            return $this->json(['message' => 'Category not found.'], Response::HTTP_NOT_FOUND);
        }

        $this->categoryRepository->delete($category);

        return $this->json([], status: Response::HTTP_NO_CONTENT);
    }
}
