<?php

namespace App\Controller;

use App\Entity\Menu;
use DateTimeImmutable;
use OpenApi\Attributes as OA;
use App\Repository\MenuRepository;
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

#[Route('api/restaurant/{id}/menu', name: 'app_api_restaurant_menu_')]
class MenuController extends AbstractController
{
    public function __construct(private EntityManagerInterface $manager, private MenuRepository $repository, private RestaurantRepository $restaurantRepository, private SerializerInterface $serializer, private UrlGeneratorInterface $urlGenerator, private MenuCategoryRepository $menuCategoryRepository)
    {
    }

    #[Route(methods: 'POST')]
    #[OA\Tag(name: "CRUD Menu")]
    #[OA\Post(
        path: "/api/restaurant/{id}/menu",
        summary: "Créer un menu",
        parameters: [new OA\Parameter(
            name: "id",
            in: "path",
            required: true,
            description: "L'identifiant du restaurant",
            schema: new OA\Schema(type: "integer", example: 1)
        )],
        requestBody: new OA\RequestBody(
            required: true,
            description: "Les données du menu à créer",
            content: new OA\JsonContent(
                type: "object",
                properties: [
                    new OA\Property(property: "title", type: "string", example: "Menu 1"),
                    new OA\Property(property: "description", type: "longtext", example: "Description du menu 1"),
                    new OA\Property(property: "price", type: "float", example: 10.99),
                    new OA\Property(property: "restaurant", type: "integer", example: 1)
                ]
            )
        ),
        responses: [new OA\Response(
            response: "201",
            description: "Menu créé avec succès",
            content: new OA\JsonContent(
                type: "object",
                properties: [
                    new OA\Property(property: "title", type: "string", example: "Menu 1"),
                    new OA\Property(property: "description", type: "longtext", example: "Description du menu 1"),
                    new OA\Property(property: "price", type: "float", example: 10.99),
                    new OA\Property(property: "createdAt", type: "string", format: "date-time", example: "31-12-2022 20:00"),
                    new OA\Property(property: "restaurant", type: "integer", example: 1)
                ]
            )
        )]
    )]
    public function new(Request $request, int $id): JsonResponse
    {
        $menu = $this->serializer->deserialize($request->getContent(), Menu::class, 'json');

        $restaurant = $this->restaurantRepository->find($id);

        if ($restaurant) {
            $menu->setRestaurant($restaurant);
            $menu->setCreatedAt(new DateTimeImmutable());

            $this->manager->persist($menu);
            $this->manager->flush();

            $responseData = $this->serializer->serialize($menu, 'json', [AbstractNormalizer::IGNORED_ATTRIBUTES => ['restaurant', 'client']]);
            $location = $this->urlGenerator->generate('app_api_restaurant_menu_show', ['id' => $restaurant->getId(), 'menuId' => $menu->getId()], UrlGeneratorInterface::ABSOLUTE_URL);

            return new JsonResponse($responseData, Response::HTTP_CREATED, ['Location' => $location], true);
        }

        return new JsonResponse(null, Response::HTTP_NOT_FOUND);
    }

    #[Route('/{menuId}', name: 'show', methods: 'GET')]
    #[OA\Tag(name: "CRUD Menu")]
    #[OA\Get(
        path: "/api/restaurant/{id}/menu/{menuId}",
        summary: "Afficher un menu",
        parameters: [new OA\Parameter(
            name: "id",
            in: "path",
            required: true,
            description: "Le menu à afficher",
            schema: new OA\Schema(type: "integer", example: 1)
        ), new OA\Parameter(
            name: "menuId",
            in: "path",
            required: true,
            description: "L'identifiant du menu à afficher",
            schema: new OA\Schema(type: "integer", example: 1)
        )],
        responses: [new OA\Response(
            response: "200",
            description: "Menu trouvé",
            content: new OA\JsonContent(
                type: "object",
                properties: [
                    new OA\Property(property: "id", type: "integer", example: 1),
                    new OA\Property(property: "title", type: "string", example: "Menu 1"),
                    new OA\Property(property: "description", type: "longtext", example: "Description du menu 1"),
                    new OA\Property(property: "price", type: "float", example: 10.99),
                    new OA\Property(property: "createdAt", type: "string", format: "date-time", example: "31-12-2022 20:00"),
                    new OA\Property(property: "restaurant", type: "integer", example: 1)
                ]
            )
        )]
    )]
    public function show(int $id, int $menuId): JsonResponse
    {
        $menu = $this->repository->findOneBy(['id' => $menuId, 'restaurant' => $id]);

        if ($menu) {
            $responseData = $this->serializer->serialize($menu, 'json', [AbstractNormalizer::IGNORED_ATTRIBUTES => ['restaurant']]);

            return new JsonResponse($responseData);
        }

        return new JsonResponse(null, Response::HTTP_NOT_FOUND);
    }

    #[Route('/{menuId}', name: 'edit', methods: 'PUT')]
    #[OA\Tag(name: "CRUD Menu")]
    #[OA\Put(
        path: "/api/restaurant/{id}/menu/{menuId}",
        summary: "Modifier un menu",
        parameters: [new OA\Parameter(
            name: "id",
            in: "path",
            required: true,
            description: "L'identifiant du restaurant",
            schema: new OA\Schema(type: "integer", example: 1)
        ), new OA\Parameter(
            name: "menuId",
            in: "path",
            required: true,
            description: "L'identifiant du à modifier",
            schema: new OA\Schema(type: "integer", example: 1)
        )],
        requestBody: new OA\RequestBody(
            required: true,
            description: "Les données du menu' à modifier",
            content: new OA\JsonContent(
                type: "object",
                properties: [
                    new OA\Property(property: "title", type: "string", example: "Menu 1"),
                    new OA\Property(property: "description", type: "longtext", example: "Description du menu 1"),
                    new OA\Property(property: "price", type: "float", example: 10.99),
                    new OA\Property(property: "restaurant", type: "integer", example: 1)
                ]
            )
        ),
        responses: [new OA\Response(
            response: "204",
            description: "Menu modifié avec succès",
        ), new OA\Response(
            response: "404",
            description: "Menu non trouvé"
        )]
    )]
    public function edit(Request $request, int $id, int $menuId): JsonResponse
    {
        $menu = $this->repository->findOneBy(['id' => $menuId, 'restaurant' => $id]);

        if ($menu) {
            $this->serializer->deserialize($request->getContent(), Menu::class, 'json', [AbstractNormalizer::OBJECT_TO_POPULATE => $menu]);
            $menu->setUpdatedAt(new DateTimeImmutable());
            $menu->setRestaurant($this->restaurantRepository->find($id));

            $this->manager->flush();

            return new JsonResponse(null, Response::HTTP_NO_CONTENT);
        }

        return new JsonResponse(null, Response::HTTP_NOT_FOUND);
    }

    #[Route('/{menuId}', name: 'delete', methods: 'DELETE')]
    #[OA\Tag(name: "CRUD Menu")]
    #[OA\Delete(
        path: "/api/restaurant/{id}/menu/{menuId}",
        summary: "Supprimer un menu",
        parameters: [new OA\Parameter(
            name: "id",
            in: "path",
            required: true,
            description: "L'identifiant du restaurant",
            schema: new OA\Schema(type: "integer", example: 1)
        ), new OA\Parameter(
            name: "menuId",
            in: "path",
            required: true,
            description: "L'identifiant du menu à supprimer",
            schema: new OA\Schema(type: "integer", example: 1)
        )],
        responses: [new OA\Response(
            response: "204",
            description: "Menu supprimé avec succès"
        )]
    )]
    public function delete(int $id, int $menuId): JsonResponse
    {
        $menu = $this->repository->findOneBy(['id' => $menuId, 'restaurant' => $id]);

        if ($menu) {
            $this->manager->remove($menu);
            $this->manager->flush();

            return new JsonResponse(null, Response::HTTP_NO_CONTENT);
        }

        return new JsonResponse(null, Response::HTTP_NOT_FOUND);
    }

    #[Route('s', name: 'list', methods: 'GET')]
    #[OA\Tag(name: "CRUD Menu")]
    #[OA\Get(
        path: "/api/restaurant/{id}/menus",
        summary: "Afficher les menus d'un restaurant",
        parameters: [new OA\Parameter(
            name: "id",
            in: "path",
            required: true,
            description: "L'identifiant du restaurant",
            schema: new OA\Schema(type: "integer", example: 1)
        )],
        responses: [new OA\Response(
            response: "200",
            description: "Menus trouvés",
        ), new OA\Response(
            response: "404",
            description: "Menus non trouvés"
        )]
    )]
    public function list(int $id): JsonResponse
    {
        $menus = $this->repository->findBy(['restaurant' => $id]);

        if ($menus) {
            $responseData = $this->serializer->serialize($menus, 'json', ['groups' => 'menu']);
            return new JsonResponse($responseData);
        }

        return new JsonResponse(null, Response::HTTP_NOT_FOUND);
    }
}
