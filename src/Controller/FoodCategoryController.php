<?php

namespace App\Controller;


use App\Entity\FoodCategory;
use OpenApi\Attributes as OA;
use App\Repository\FoodRepository;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\FoodCategoryRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('api/restaurant/food/category', name: 'app_api_restaurant_food_category_')]
#[OA\Tag(name: "CRUD Catégorie de plat")]
class FoodCategoryController extends AbstractController
{
    public function __construct(private EntityManagerInterface $manager, private FoodCategoryRepository $repository,  private SerializerInterface $serializer, private UrlGeneratorInterface $urlGenerator, private FoodRepository $foodRepository, private CategoryRepository $categoryRepository)
    {
    }
    #[Route(methods: 'POST')]
    #[OA\Post(
        path: "/api/restaurant/food/category",
        summary: "Créer une association entre un plat et une catégorie",
        requestBody: new OA\RequestBody(
            required: true,
            description: "Les données de l'association à créer",
            content: new OA\JsonContent(
                type: "object",
                properties: [
                    new OA\Property(property: "food", type: "integer", example: 1),
                    new OA\Property(property: "category", type: "integer", example: 1),
                ]
            )
        ),
        responses: [new OA\Response(
            response: "201",
            description: "Association créée avec succès",
            content: new OA\JsonContent(
                type: "object",
                properties: [
                    new OA\Property(property: "id", type: "integer", example: 1),
                    new OA\Property(property: "food", type: "integer", example: 1),
                    new OA\Property(property: "category", type: "integer", example: 1),
                ]
            )
        )]
    )]
    public function new(Request $request): JsonResponse
    {
        $food = $this->foodRepository->find($request->toArray()['food']);
        $category = $this->categoryRepository->find($request->toArray()['category']);

        if (!$food || !$category) {
            return new JsonResponse('Food or Category not found', Response::HTTP_NOT_FOUND);
        }

        $foodCategory = $this->serializer->deserialize($request->getContent(), FoodCategory::class, 'json');

        $foodCategory->setFood($food);
        $foodCategory->setCategory($category);

        $this->manager->persist($foodCategory);
        $this->manager->flush();

        $responseData = $this->serializer->serialize($foodCategory, 'json', [AbstractNormalizer::IGNORED_ATTRIBUTES => ['food', 'category']]);
        $location = $this->urlGenerator->generate('app_api_restaurant_food_category_read', ['id' => $foodCategory->getId()]);
        return new JsonResponse($responseData, Response::HTTP_CREATED, ['Location' => $location], true);
    }

    #[Route('/{id}', name: 'read', methods: 'GET')]
    #[OA\Get(
        path: "/api/restaurant/food/category/{id}",
        summary: "Afficher une association entre un plat et une catégorie",
        parameters: [new OA\Parameter(
            name: "id",
            in: "path",
            required: true,
            description: "ID de l'association",
            schema: new OA\Schema(type: "integer", example: 1)
        )],
        responses: [new OA\Response(
            response: "200",
            description: "Association trouvée",
            content: new OA\JsonContent(
                type: "object",
                properties: [
                    new OA\Property(property: "id", type: "integer", example: 1),
                    new OA\Property(property: "food", type: "integer", example: 1),
                    new OA\Property(property: "category", type: "integer", example: 1),
                ]
            )
        )]
    )]
    public function read(FoodCategory $foodCategory): JsonResponse
    {
        $foodCategory = $this->repository->find($foodCategory);

        if ($foodCategory) {
            $responseData = $this->serializer->serialize($foodCategory, 'json', [AbstractNormalizer::IGNORED_ATTRIBUTES => ['food', 'category']]);
            return new JsonResponse($responseData);
        }

        return new JsonResponse('Food Category not found', Response::HTTP_NOT_FOUND);
    }

    #[Route('/{id}', name: 'update', methods: 'PUT')]
    #[OA\Put(
        path: "/api/restaurant/food/category/{id}",
        summary: "Modifier une association entre un plat et une catégorie",
        parameters: [new OA\Parameter(
            name: "id",
            in: "path",
            required: true,
            description: "ID de l'association",
            schema: new OA\Schema(type: "integer", example: 1)
        )],
        requestBody: new OA\RequestBody(
            required: true,
            description: "Les données de l'association à modifier",
            content: new OA\JsonContent(
                type: "object",
                properties: [
                    new OA\Property(property: "food", type: "integer", example: 1),
                    new OA\Property(property: "category", type: "integer", example: 1),
                ]
            )
        ),
        responses: [new OA\Response(
            response: "200",
            description: "Association modifiée avec succès",
            content: new OA\JsonContent(
                type: "object",
                properties: [
                    new OA\Property(property: "id", type: "integer", example: 1),
                    new OA\Property(property: "food", type: "integer", example: 1),
                    new OA\Property(property: "category", type: "integer", example: 1),
                ]
            )
        )]
    )]
    public function update(Request $request, FoodCategory $foodCategory): JsonResponse
    {
        $food = $this->foodRepository->find($request->toArray()['food']);
        $category = $this->categoryRepository->find($request->toArray()['category']);

        $foodCategory->setFood($food);
        $foodCategory->setCategory($category);

        $this->manager->flush();

        $responseData = $this->serializer->serialize($foodCategory, 'json', [AbstractNormalizer::IGNORED_ATTRIBUTES => ['food', 'category']]);
        return new JsonResponse($responseData);
    }

    #[Route('/{id}', name: 'delete', methods: 'DELETE')]
    #[OA\Delete(
        path: "/api/restaurant/food/category/{id}",
        summary: "Supprimer une association entre un plat et une catégorie",
        parameters: [new OA\Parameter(
            name: "id",
            in: "path",
            required: true,
            description: "ID de l'association",
            schema: new OA\Schema(type: "integer", example: 1)
        )],
        responses: [new OA\Response(
            response: "204",
            description: "Association supprimée avec succès"
        )]
    )]
    public function delete(FoodCategory $foodCategory): JsonResponse
    {
        $this->manager->remove($foodCategory);
        $this->manager->flush();

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }

    #[Route('-list', name: 'list', methods: 'GET')]
    #[OA\Get(
        path: "/api/restaurant/food/category-list",
        summary: "Afficher les plats d'une catégorie",
        responses: [
            new OA\Response(
                response: "200",
                description: "Associations trouvées",
            ),
            new OA\Response(
                response: "404",
                description: "Associations non trouvées"
            )
        ]
    )]
    public function list(): JsonResponse
    {
        $foodCategories = $this->repository->findAll();

        if ($foodCategories) {
            $responseData = $this->serializer->serialize($foodCategories, 'json', [AbstractNormalizer::IGNORED_ATTRIBUTES => ['food', 'category']]);
            return new JsonResponse($responseData);
        }

        return new JsonResponse(null, Response::HTTP_NOT_FOUND);
    }
}
