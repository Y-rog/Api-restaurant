<?php

namespace App\Controller;

use App\Entity\MenuCategory;
use OpenApi\Attributes as OA;
use App\Repository\MenuRepository;
use App\Repository\CategoryRepository;
use App\Repository\RestaurantRepository;
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

#[Route('/api/restaurant/menu/{id}/category', name: '/api_restaurant_menu_category_')]
class MenuCategoryController extends AbstractController
{
    public function __construct(private EntityManagerInterface $manager, private MenuCategoryRepository $repository, private SerializerInterface $serializer, private UrlGeneratorInterface $urlGenerator, private RestaurantRepository $restaurantRepository, private MenuRepository $menuRepository, private CategoryRepository $categoryRepository)
    {
    }

    #[Route(methods: 'POST')]
    #[OA\Tag(name: "CRUD MenuCategory")]
    #[OA\Post(
        path: "/api/restaurant/menu/{id}/category",
        summary: "Ajouter une catégorie au menu dont l'identifiant est passé en paramètre",
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                required: true,
                description: "L'identifiant du Menu",
                schema: new OA\Schema(type: "integer", example: 1)
            )
        ],
        requestBody: new OA\RequestBody(
            required: true,
            description: "Les données de la catégorie de menu à ajouter",
            content: new OA\JsonContent(
                type: "object",
                properties: [
                    new OA\Property(property: "categoryId", type: "integer", example: 1)
                ]
            )
        ),
        responses: [new OA\Response(
            response: "201",
            description: "Ajout de catégorie au menu avec succès",
            content: new OA\JsonContent(
                type: "object",
                properties: [
                    new OA\Property(property: "id", type: "integer", example: 1),
                    new OA\Property(property: "category", type: "integer", example: 1),
                    new OA\Property(property: "menuId", type: "integer", example: 1)
                ]
            )
        )]
    )]
    public function add(int $id, Request $request): JsonResponse
    {
        $menuCategory = $this->serializer->deserialize($request->getContent(), MenuCategory::class, 'json', [AbstractNormalizer::IGNORED_ATTRIBUTES => ['menuId', 'categoryId'], AbstractNormalizer::DEFAULT_CONSTRUCTOR_ARGUMENTS => ['menuId' => $id]]);

        $menuId = $this->menuRepository->findOneBy(['id' => $id]);

        if ($menuId) {

            $menuCategory->setMenu($menuId);

            $this->manager->persist($menuCategory);
            $this->manager->flush();

            $responseData = $this->serializer->serialize($menuCategory, 'json');
            $location = $this->urlGenerator->generate('api_restaurant_menu_category_show', ['id' => $menuCategory->getId()], UrlGeneratorInterface::ABSOLUTE_URL);

            return new JsonResponse($responseData, Response::HTTP_CREATED, ['Location' => $location], true);
        }
        return new JsonResponse(null, Response::HTTP_NOT_FOUND);
    }






    #[Route('/{menucategoryId}', name: 'show', methods: 'GET')]
    #[OA\Tag(name: "CRUD MenuCategory")]
    #[OA\Get(
        path: "/api/restaurant/{id}/menucategory/{menucategoryId}",
        summary: "Afficher une catégorie de menu",
        parameters: [new OA\Parameter(
            name: "id",
            in: "path",
            required: true,
            description: "L'identifiant du restaurant",
            schema: new OA\Schema(type: "integer", example: 1)
        ), new OA\Parameter(
            name: "menucategoryId",
            in: "path",
            required: true,
            description: "L'identifiant de la catégorie de menu",
            schema: new OA\Schema(type: "integer", example: 1)
        )],
        responses: [new OA\Response(
            response: "200",
            description: "Catégorie de menu trouvée avec succès",
            content: new OA\JsonContent(
                type: "object",
                properties: []
            )
        )]
    )]
    public function show(int $id, int $menucategoryId): JsonResponse
    {
        $menuCategory = $this->repository->findOneBy(['id' => $menucategoryId], ['restaurant' => $id]);

        if ($menuCategory) {
            $responseData = $this->serializer->serialize($menuCategory, 'json');
            return new JsonResponse($responseData, Response::HTTP_OK, [], true);
        }
        return new JsonResponse(null, Response::HTTP_NOT_FOUND);
    }

    #[Route('/{menucategoryId}', name: 'update', methods: 'PUT')]
    #[OA\Tag(name: "CRUD MenuCategory")]
    #[OA\Put(
        path: "/api/restaurant/{id}/menucategory/{menucategoryId}",
        summary: "Modifier une catégorie de menu",
        parameters: [new OA\Parameter(
            name: "id",
            in: "path",
            required: true,
            description: "L'identifiant du restaurant",
            schema: new OA\Schema(type: "integer", example: 1)
        ), new OA\Parameter(
            name: "menucategoryId",
            in: "path",
            required: true,
            description: "L'identifiant de la catégorie de menu",
            schema: new OA\Schema(type: "integer", example: 1)
        )],
        requestBody: new OA\RequestBody(
            required: true,
            description: "Les données de la catégorie de menu à modifier",
            content: new OA\JsonContent(
                type: "object",
                properties: []
            )
        ),
        responses: [new OA\Response(
            response: "200",
            description: "Catégorie de menu modifiée avec succès",
            content: new OA\JsonContent(
                type: "object",
                properties: []
            )
        )]
    )]
    public function update(int $id, int $menucategoryId, Request $request): JsonResponse
    {
        $menuCategory = $this->repository->findOneBy(['id' => $menucategoryId], ['restaurant' => $id]);

        if ($menuCategory) {
            $menuCategory = $this->serializer->deserialize($request->getContent(), MenuCategory::class, 'json', [AbstractNormalizer::OBJECT_TO_POPULATE => $menuCategory]);

            $this->manager->flush();

            $responseData = $this->serializer->serialize($menuCategory, 'json');
            return new JsonResponse($responseData, Response::HTTP_OK, [], true);
        }
        return new JsonResponse(null, Response::HTTP_NOT_FOUND);
    }

    #[Route('/{menucategoryId}', name: 'delete', methods: 'DELETE')]
    #[OA\Tag(name: "CRUD MenuCategory")]
    #[OA\Delete(
        path: "/api/restaurant/{id}/menucategory/{menucategoryId}",
        summary: "Supprimer une catégorie de menu",
        parameters: [new OA\Parameter(
            name: "id",
            in: "path",
            required: true,
            description: "L'identifiant du restaurant",
            schema: new OA\Schema(type: "integer", example: 1)
        ), new OA\Parameter(
            name: "menucategoryId",
            in: "path",
            required: true,
            description: "L'identifiant de la catégorie de menu",
            schema: new OA\Schema(type: "integer", example: 1)
        )],
        responses: [new OA\Response(
            response: "204",
            description: "Catégorie de menu supprimée avec succès"
        )]
    )]
    public function delete(int $id, int $menucategoryId): JsonResponse
    {
        $menuCategory = $this->repository->findOneBy(['id' => $menucategoryId], ['restaurant' => $id]);

        if ($menuCategory) {
            $this->manager->remove($menuCategory);
            $this->manager->flush();

            return new JsonResponse(null, Response::HTTP_NO_CONTENT);
        }
        return new JsonResponse(null, Response::HTTP_NOT_FOUND);
    }
}
