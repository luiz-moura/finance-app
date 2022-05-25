<?php

namespace App\Controller\API;

use App\Entity\Category;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CategoryController extends AbstractController
{
    public function __construct(private CategoryRepository $categoryRepository) {}

    #[Route('api/category', name: 'app_category_api.index', methods: ['GET'])]
    public function index(): Response
    {
        $categories = $this->categoryRepository->findAll();
        $categoriesArray = (new ArrayCollection($categories))
            ->map(fn ($category) => $category->toArray())->toArray();

        return $this->json($categoriesArray);
    }

    #[Route('api/category/{id}', name: 'app_category_api.show', methods: ['GET'])]
    public function show(int $id): Response
    {
        $category = $this->categoryRepository->find($id);
        return $this->json($category->toArray());
    }

    #[Route('api/category', name: 'app_category_api.store', methods: ['POST'])]
    public function store(Request $request, EntityManagerInterface $em, ValidatorInterface $validator): Response
    {
        $paramenters = json_decode($request->getContent(), true);

        $category = new Category();
        $category->setName($paramenters['name']);
        $category->setBackground($paramenters['background']);

        $errors = $validator->validate($category);

        if (count($errors) > 0) {
            return $this->json(['error' => $errors], 400);
        }

        $em->persist($category);
        $em->flush();

        return $this->json([
            'message'   => 'Category saved with success',
            'category'  => $category->toArray()
        ]);
    }

    #[Route('api/category/{id}', name: 'app_category_api.update', methods: ['PUT'])]
    public function update(Request $request, int $id, EntityManagerInterface $em, ValidatorInterface $validator): Response
    {
        $category = $this->categoryRepository->find($id);

        if (!$category) {
            return $this->json(['error' => 'No category found for id ' . $id, 404]);
        }

        $paramenters = json_decode($request->getContent(), true);

        $category->setName($paramenters['name']);
        $category->setBackground($paramenters['background']);

        $errors = $validator->validate($category);

        if (count($errors) > 0) {
            return $this->json(['error' => $errors], 400);
        }

        $em->flush();

        return $this->json([
            'message'   => 'Category updated with success',
            'category'  => $category->toArray()
        ]);
    }

    #[Route('api/category/{id}', name: 'app_category_api.delete', methods: ['DELETE'])]
    public function delete(int $id, EntityManagerInterface $em): Response
    {
        $category = $this->categoryRepository->find($id);

        if (!$category) {
            return $this->json(['error' => 'No category found for id ' . $id, 404]);
        }

        $em->remove($category);
        $em->flush();

        return $this->json(['message' => 'Category deleted with success'], status: 204);
    }
}
