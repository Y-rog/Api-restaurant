<?php

namespace App\Controller;

use OpenApi\Attributes as OA;
use App\Entity\Food;
use DateTimeImmutable;
use App\Repository\FoodRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('api/restaurant/food', name: 'app_api_restaurant_food_')]
class FoodController extends AbstractController
{
    public function __construct(private EntityManagerInterface $manager, private FoodRepository $repository, private SerializerInterface $serializer, private UrlGeneratorInterface $urlGenerator)
    {
    }

    #[Route(methods: 'POST')]
    #[OA\Tag(name: "CRUD Food")]
    #[OA\Post(
        path: "/api/restaurant/food",
        summary: "Créer un plat",
        requestBody: new OA\RequestBody(
            required: true,
            description: "Les données du plat à créer",
            content: new OA\JsonContent(
                type: "object",
                properties: [
                    new OA\Property(property: "title", type: "string", example: "Nom du plat"),
                    new OA\Property(property: "description", type: "string", example: "Description du plat"),
                    new OA\Property(property: "price", type: "float", example: 10.99),
                    new OA\Property(property: "createdAt", type: "string", format: "date-time"),

                ]
            )
        ),
        responses: [new OA\Response(
            response: "201",
            description: "Plat créé avec succès",
            content: new OA\JsonContent(
                type: "object",
                properties: [
                    new OA\Property(property: "id", type: "integer", example: 1),
                    new OA\Property(property: "title", type: "string", example: "Nom du plat"),
                    new OA\Property(property: "description", type: "string", example: "Description du plat"),
                    new OA\Property(property: "price", type: "integer", example: 10.99),
                    new OA\Property(property: "createdAt", type: "string", format: "date-time"),
                ]
            )
        )]
    )]
    public function new(Request $request): JsonResponse
    {
        $food = $this->serializer->deserialize($request->getContent(), Food::class, 'json');
        $food->setCreatedAt(new DateTimeImmutable());

        $this->manager->persist($food);
        $this->manager->flush();

        $responseData = $this->serializer->serialize($food, 'json');
        $location = $this->urlGenerator->generate('app_api_restaurant_food_show', ['id' => $food->getId()], UrlGeneratorInterface::ABSOLUTE_URL);

        return new JsonResponse($responseData, Response::HTTP_CREATED, ['Location' => $location], true);
    }

    #[Route('/{id}', name: 'show', methods: 'GET')]
    #[OA\Tag(name: "CRUD Food")]
    #[OA\Get(
        path: "/api/restaurant/food/{id}",
        summary: "Afficher un plat",
        parameters: [new OA\Parameter(
            name: "id",
            in: "path",
            description: "L'identifiant du plat à afficher",
            required: true,
            schema: new OA\Schema(type: "integer", format: "integer", example: 1)
        )],
        responses: [new OA\Response(
            response: "200",
            description: "Plat trouvé avec succès",
            content: new OA\JsonContent(
                type: "object",
                properties: [
                    new OA\Property(property: "id", type: "integer", example: 1),
                    new OA\Property(property: "title", type: "string", example: "Nom du plat"),
                    new OA\Property(property: "description", type: "string", example: "Description du plat"),
                    new OA\Property(property: "price", type: "integer", example: 10.99),
                    new OA\Property(property: "createdAt", type: "string", format: "date-time"),
                    new OA\Property(property: "updatedAt", type: "string", format: "date-time"),
                ]
            )
        )]
    )]
    public function show(int $id): JsonResponse
    {
        $food = $this->repository->findOneBy(['id' => $id]);
        if ($food) {
            $responseData = $this->serializer->serialize($food, 'json');

            return new JsonResponse($responseData, Response::HTTP_OK, [], true);
        }

        return new JsonResponse(null, Response::HTTP_NOT_FOUND);
    }

    #[Route('/{id}', name: 'edit', methods: 'PUT')]
    #[OA\Tag(name: "CRUD Food")]
    #[OA\Put(
        path: "/api/restaurant/food/{id}",
        summary: "Modifier un plat",
        parameters: [new OA\Parameter(
            name: "id",
            in: "path",
            description: "L'identifiant du plat à modifier",
            required: true,
            schema: new OA\Schema(type: "integer", format: "integer", example: 1)
        )],
        requestBody: new OA\RequestBody(
            required: true,
            description: "Les données du plat à modifier",
            content: new OA\JsonContent(
                type: "object",
                properties: [
                    new OA\Property(property: "id", type: "integer", example: 1),
                    new OA\Property(property: "title", type: "string", example: "Nom du plat"),
                    new OA\Property(property: "description", type: "string", example: "Description du plat"),
                    new OA\Property(property: "price", type: "integer", example: 10.99),
                    new OA\Property(property: "updatedAt", type: "string", format: "date-time"),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: "204",
                description: "Plat modifié avec succès",
            ),
            new OA\Response(
                response: "404",
                description: "Plat non trouvé"
            )
        ]
    )]
    public function edit(int $id, Request $request): JsonResponse
    {
        $food = $this->repository->findOneBy(['id' => $id]);
        if ($food) {
            $food = $this->serializer->deserialize($request->getContent(), Food::class, 'json', [AbstractNormalizer::OBJECT_TO_POPULATE => $food]);
            $food->setUpdatedAt(new DateTimeImmutable());

            $this->manager->flush();

            return new JsonResponse(null, Response::HTTP_NO_CONTENT);
        }

        return new JsonResponse(null, Response::HTTP_NOT_FOUND);
    }


    #[Route('/{id}', name: 'delete', methods: 'DELETE')]
    #[OA\Tag(name: "CRUD Food")]
    #[OA\Delete(
        path: "/api/restaurant/food/{id}",
        summary: "Supprimer un plat",
        parameters: [new OA\Parameter(
            name: "id",
            in: "path",
            description: "L'identifiant du plat à supprimer",
            required: true,
            schema: new OA\Schema(type: "integer", format: "integer", example: 1)
        )],
        responses: [
            new OA\Response(
                response: "204",
                description: "Plat supprimé avec succès"
            ),
            new OA\Response(
                response: "404",
                description: "Plat non trouvé"
            )
        ]
    )]
    public function delete(int $id): JsonResponse
    {
        $food = $this->repository->findOneBy(['id' => $id]);
        if ($food) {
            $this->manager->remove($food);
            $this->manager->flush();

            return new JsonResponse(null, Response::HTTP_NO_CONTENT);
        }

        return new JsonResponse(null, Response::HTTP_NOT_FOUND);
    }

    #[Route('s', name: 'list', methods: 'GET')]
    #[OA\Tag(name: "CRUD Food")]
    #[OA\Get(
        path: "/api/restaurant/foods",
        summary: "Afficher les plats",
        responses: [
            new OA\Response(
                response: "200",
                description: "Liste des plats trouvée avec succès",
            ),
            new OA\Response(
                response: "404",
                description: "Aucun plat trouvé"
            )
        ]
    )]
    public function list(): JsonResponse
    {
        $foods = $this->repository->findAll();
        $responseData = $this->serializer->serialize($foods, 'json', ['groups' => 'food']);

        return new JsonResponse($responseData, Response::HTTP_OK, [], true);
    }
}
