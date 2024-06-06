<?php

namespace App\Controller;


use App\Entity\MenuCategory;
use OpenApi\Attributes as OA;
use App\Repository\MenuRepository;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\MenuCategoryRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('api/restaurant/menu/category', name: 'app_api_restaurant_menu_category_')]
#[OA\Tag(name: "CRUD Menu Categorie")]
class MenuCategoryController extends AbstractController
{
    public function __construct(private EntityManagerInterface $manager, private MenuCategoryRepository $repository,  private SerializerInterface $serializer, private UrlGeneratorInterface $urlGenerator, private MenuRepository $menuRepository, private CategoryRepository $categoryRepository)
    {
    }
    #[Route(methods: 'POST')]
    #[OA\Post(
        path: "/api/restaurant/menu/category",
        summary: "Créer une association entre un menu et une catégorie",
        requestBody: new OA\RequestBody(
            required: true,
            description: "Les données de l'association à créer",
            content: new OA\JsonContent(
                type: "object",
                properties: [
                    new OA\Property(property: "menu", type: "integer", example: 1),
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
                    new OA\Property(property: "menu", type: "integer", example: 1),
                    new OA\Property(property: "category", type: "integer", example: 1),
                ]
            )
        )]
    )]
    public function new(Request $request): JsonResponse
    {
        $menu = $this->menuRepository->find($request->toArray()['menu']);
        $category = $this->categoryRepository->find($request->toArray()['category']);

        $menuCategory = $this->serializer->deserialize($request->getContent(), MenuCategory::class, 'json');

        $menuCategory->setMenu($menu);
        $menuCategory->setCategory($category);

        $this->manager->persist($menuCategory);
        $this->manager->flush();

        $responseData = $this->serializer->serialize($menuCategory, 'json', [AbstractNormalizer::IGNORED_ATTRIBUTES => ['menu', 'category']]);
        $location = $this->urlGenerator->generate('app_api_restaurant_menu_category_read', ['id' => $menuCategory->getId()]);
        return new JsonResponse($responseData, Response::HTTP_CREATED, ['Location' => $location], true);
    }

    #[Route('/{id}', name: 'read', methods: 'GET')]
    #[OA\Get(
        path: "/api/restaurant/menu/category/{id}",
        summary: "Afficher une association entre un menu et une catégorie",
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
                    new OA\Property(property: "menu", type: "integer", example: 1),
                    new OA\Property(property: "category", type: "integer", example: 1),
                ]
            )
        )]
    )]
    public function read(MenuCategory $menuCategory): JsonResponse
    {
        $menuCategory = $this->repository->find($menuCategory);

        if ($menuCategory) {
            $responseData = $this->serializer->serialize($menuCategory, 'json', [AbstractNormalizer::IGNORED_ATTRIBUTES => ['menu', 'category']]);
            return new JsonResponse($responseData);
        }

        return new JsonResponse('Menu Category not found', Response::HTTP_NOT_FOUND);
    }

    #[Route('/{id}', name: 'update', methods: 'PUT')]
    #[OA\Put(
        path: "/api/restaurant/menu/category/{id}",
        summary: "Modifier une association entre un menu et une catégorie",
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
                    new OA\Property(property: "menu", type: "integer", example: 1),
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
                    new OA\Property(property: "menu", type: "integer", example: 1),
                    new OA\Property(property: "category", type: "integer", example: 1),
                ]
            )
        )]
    )]
    public function update(Request $request, MenuCategory $menuCategory): JsonResponse
    {
        $menu = $this->menuRepository->find($request->toArray()['menu']);
        $category = $this->categoryRepository->find($request->toArray()['category']);

        $menuCategory->setMenu($menu);
        $menuCategory->setCategory($category);

        $this->manager->flush();

        $responseData = $this->serializer->serialize($menuCategory, 'json', [AbstractNormalizer::IGNORED_ATTRIBUTES => ['menu', 'category']]);
        return new JsonResponse($responseData);
    }

    #[Route('/{id}', name: 'delete', methods: 'DELETE')]
    #[OA\Delete(
        path: "/api/restaurant/menu/category/{id}",
        summary: "Supprimer une association entre un menu et une catégorie",
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
    public function delete(MenuCategory $menuCategory): JsonResponse
    {
        $this->manager->remove($menuCategory);
        $this->manager->flush();

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }

    #[Route('-list', name: 'list', methods: 'GET')]
    #[OA\Get(
        path: "/api/restaurant/menu/category-list",
        summary: "Liste des associations entre un menu et une catégorie",
        responses: [
            new OA\Response(
                response: "200",
                description: "Liste des associations trouvée",
            ),
            new OA\Response(
                response: "404",
                description: "Aucune association trouvée"
            )
        ]
    )]
    public function list(): JsonResponse
    {
        $menuCategories = $this->repository->findAll();

        if ($menuCategories) {
            $responseData = $this->serializer->serialize($menuCategories, 'json', [AbstractNormalizer::IGNORED_ATTRIBUTES => ['menu', 'category']]);
            return new JsonResponse($responseData);
        }

        return new JsonResponse('Menu Categories not found', Response::HTTP_NOT_FOUND);
    }
}
