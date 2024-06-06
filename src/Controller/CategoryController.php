<?php

namespace App\Controller;

use App\Entity\Food;
use DateTimeImmutable;
use App\Entity\Category;
use App\Entity\FoodCategory;
use OpenApi\Attributes as OA;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('api/restaurant/category', name: 'app_api_restaurant_category_')]
#[OA\Tag(name: "CRUD Catégorie")]
class CategoryController extends AbstractController
{
    public function __construct(private EntityManagerInterface $manager, private CategoryRepository $repository, private SerializerInterface $serializer, private UrlGeneratorInterface $urlGenerator)
    {
    }

    #[Route(methods: 'POST')]
    #[OA\Post(
        path: "/api/restaurant/category",
        summary: "Créer une catégorie",
        requestBody: new OA\RequestBody(
            required: true,
            description: "Les données de la catégorie à créer",
            content: new OA\JsonContent(
                type: "object",
                properties: [
                    new OA\Property(property: "title", type: "string", example: "Nom de la catégorie"),
                    new OA\Property(property: "createdAt", type: "string", format: "date-time"),
                ]
            )
        ),
        responses: [new OA\Response(
            response: "201",
            description: "Catégorie créée avec succès",
            content: new OA\JsonContent(
                type: "object",
                properties: [
                    new OA\Property(property: "id", type: "integer", example: 1),
                    new OA\Property(property: "title", type: "string", example: "Nom de la catégorie"),
                    new OA\Property(property: "createdAt", type: "string", format: "date-time"),
                ]
            )
        )]
    )]
    public function new(Request $request): JsonResponse
    {
        $category = $this->serializer->deserialize($request->getContent(), Category::class, 'json');
        $category->setCreatedAt(new DateTimeImmutable());

        $this->manager->persist($category);
        $this->manager->flush();

        $responseData = $this->serializer->serialize($category, 'json');
        $location = $this->urlGenerator->generate('app_api_restaurant_category_show', ['id' => $category->getId()]);

        return new JsonResponse($responseData, Response::HTTP_CREATED, ['Location' => $location], true);
    }

    #[Route('/{id}', name: 'show', methods: 'GET')]
    #[OA\Get(
        path: "/api/restaurant/category/{id}",
        summary: "Afficher une catégorie",
        parameters: [new OA\Parameter(
            name: "id",
            in: "path",
            description: "L'identifiant de la catégorie à afficher",
            required: true,
            schema: new OA\Schema(type: "integer", format: "integer", example: 1)
        )],
        responses: [
            new OA\Response(
                response: "200",
                description: "Catégorie trouvée avec succès",
                content: new OA\JsonContent(
                    type: "object",
                    properties: [
                        new OA\Property(property: "id", type: "integer", example: 1),
                        new OA\Property(property: "title", type: "string", example: "Nom de la catégorie"),
                        new OA\Property(property: "createdAt", type: "string", format: "date-time"),
                    ]
                )
            ),
            new OA\Response(
                response: "404",
                description: "Catégorie non trouvée"
            )
        ]
    )]
    public function show(int $id): JsonResponse
    {
        $category = $this->repository->findOneBy(['id' => $id]);
        if ($category) {
            $responseData = $this->serializer->serialize($category, 'json');

            return new JsonResponse($responseData, Response::HTTP_OK, [], true);
        }

        return new JsonResponse(null, Response::HTTP_NOT_FOUND);
    }

    #[Route('/{id}', name: 'edit', methods: 'PUT')]
    #[OA\Put(
        path: "/api/restaurant/category/{id}",
        summary: "Modifier une catégorie",
        parameters: [new OA\Parameter(
            name: "id",
            in: "path",
            description: "L'identifiant de la catégorie à modifier",
            required: true,
            schema: new OA\Schema(type: "integer", format: "integer", example: 1)
        )],
        requestBody: new OA\RequestBody(
            required: true,
            description: "Les données de la catégorie à modifier",
            content: new OA\JsonContent(
                type: "object",
                properties: [
                    new OA\Property(property: "title", type: "string", example: "Nom de la catégorie"),
                    new OA\Property(property: "updatedAt", type: "string", format: "date-time"),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: "204",
                description: "Catégorie modifiée avec succès",
            ),
            new OA\Response(
                response: "404",
                description: "Catégorie non trouvée"
            )
        ]
    )]
    public function edit(int $id, Request $request): JsonResponse
    {
        $category = $this->repository->findOneBy(['id' => $id]);
        if ($category) {
            $category = $this->serializer->deserialize($request->getContent(), Category::class, 'json', [AbstractNormalizer::OBJECT_TO_POPULATE => $category]);
            $category->setUpdatedAt(new DateTimeImmutable());

            $this->manager->flush();

            return new JsonResponse(null, Response::HTTP_NO_CONTENT);
        }

        return new JsonResponse(null, Response::HTTP_NOT_FOUND);
    }


    #[Route('/{id}', name: 'delete', methods: 'DELETE')]
    #[OA\Delete(
        path: "/api/restaurant/category/{id}",
        summary: "Supprimer une catégorie",
        parameters: [new OA\Parameter(
            name: "id",
            in: "path",
            description: "L'identifiant de la catégorie à supprimer",
            required: true,
            schema: new OA\Schema(type: "integer", format: "integer", example: 1)
        )],
        responses: [
            new OA\Response(
                response: "204",
                description: "Catégorie supprimée avec succès"
            ),
            new OA\Response(
                response: "404",
                description: "Catégorie non trouvée"
            )
        ]
    )]
    public function delete(int $id): JsonResponse
    {
        $category = $this->repository->findOneBy(['id' => $id]);
        if ($category) {
            $this->manager->remove($category);
            $this->manager->flush();

            return new JsonResponse(null, Response::HTTP_NO_CONTENT);
        }

        return new JsonResponse(null, Response::HTTP_NOT_FOUND);
    }

    #[Route('-list', name: 'list', methods: 'GET')]
    #[OA\Get(
        path: "/api/restaurant/category-list",
        summary: "Afficher les catégories",
        responses: [
            new OA\Response(
                response: "200",
                description: "Catégories trouvées avec succès",
            ),
            new OA\Response(
                response: "404",
                description: "Aucune catégorie trouvée"
            )
        ]
    )]
    public function list(): JsonResponse
    {
        $categories = $this->repository->findAll();
        if ($categories) {
            $responseData = $this->serializer->serialize($categories, 'json', ['groups' => 'category']);

            return new JsonResponse($responseData);
        }

        return new JsonResponse(null, Response::HTTP_NOT_FOUND);
    }
}
