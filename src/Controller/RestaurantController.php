<?php

namespace App\Controller;

use DateTimeImmutable;
use App\Entity\Restaurant;
use App\Repository\RestaurantRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use OpenApi\Attributes as OA;

#[Route('api/restaurant', name: 'app_api_restaurant_')]
class RestaurantController extends AbstractController
{
    public function __construct(private EntityManagerInterface $manager, private RestaurantRepository $repository, private SerializerInterface $serializer, private UrlGeneratorInterface $urlGenerator)
    {
    }

    #[Route(methods: 'POST')]
    #[OA\Tag(name: "CRUD Restaurant")]
    #[OA\Post(
        path: "/api/restaurant",
        summary: "Créer un restaurant",
        requestBody: new OA\RequestBody(
            required: true,
            description: "Les données du restaurant à créer",
            content: new OA\JsonContent(
                type: "object",
                properties: [
                    new OA\Property(property: "name", type: "string", example: "Nom du restaurant"),
                    new OA\Property(property: "description", type: "string", example: "Description du restaurant"),
                    new OA\Property(property: "amOpeningTime", type: "array", items: (new OA\Items(type: "string", example: "08:00"))),
                    new OA\Property(property: "pmOpeningTime", type: "array", items: (new OA\Items(type: "string", example: "12:00"))),
                    new OA\Property(property: "maxGuest", type: "integer", example: 50),
                    new OA\Property(property: "createdAt", type: "string", format: "date-time"),
                    new OA\Property(property: "owner", type: "integer", example: 1)
                ]
            )
        ),
        responses: [new OA\Response(
            response: "201",
            description: "Restaurant créé avec succès",
            content: new OA\JsonContent(
                type: "object",
                properties: [
                    new OA\Property(property: "id", type: "integer", example: 1),
                    new OA\Property(property: "name", type: "string", example: "Nom du restaurant"),
                    new OA\Property(property: "description", type: "string", example: "Description du restaurant"),
                    new OA\Property(property: "amOpeningTime", type: "array", items: (new OA\Items(type: "string", example: "08:00"))),
                    new OA\Property(property: "pmOpeningTime", type: "array", items: (new OA\Items(type: "string", example: "12:00"))),
                    new OA\Property(property: "maxGuest", type: "integer", example: 50),
                    new OA\Property(property: "createdAt", type: "string", format: "date-time"),
                    new OA\Property(property: "owner", type: "integer", example: 1)
                ]
            )
        )]
    )]
    public function new(Request $request): JsonResponse
    {
        $restaurant = $this->serializer->deserialize($request->getContent(), Restaurant::class, 'json');
        $restaurant->setCreatedAt(new DateTimeImmutable());
        $restaurant->setOwner($this->getUser());

        $this->manager->persist($restaurant);
        $this->manager->flush();

        $responseData = $this->serializer->serialize($restaurant, 'json');
        $location = $this->urlGenerator->generate('app_api_restaurant_show', ['id' => $restaurant->getId()], UrlGeneratorInterface::ABSOLUTE_URL);

        return new JsonResponse($responseData, Response::HTTP_CREATED, ['Location' => $location], true);
    }

    #[Route('/{id}', name: 'show', methods: 'GET')]
    #[OA\Tag(name: "CRUD Restaurant")]
    #[OA\Get(
        path: "/api/restaurant/{id}",
        summary: "Afficher un restaurant par son identifiant",
        parameters: [new OA\Parameter(
            name: "id",
            in: "path",
            required: true,
            description: "L'identifiant du restaurant à afficher",
            schema: new OA\Schema(type: "integer", example: 1)
        )],
        responses: [
            new OA\Response(
                response: "200",
                description: "Restaurant trouvé avec succès",
                content: new OA\JsonContent(
                    type: "object",
                    properties: [
                        new OA\Property(property: "id", type: "integer", example: 1),
                        new OA\Property(property: "name", type: "string", example: "Nom du restaurant"),
                        new OA\Property(property: "description", type: "string", example: "Description du restaurant"),
                        new OA\Property(property: "amOpeningTime", type: "array", items: (new OA\Items(type: "string", example: "08:00"))),
                        new OA\Property(property: "pmOpeningTime", type: "array", items: (new OA\Items(type: "string", example: "12:00"))),
                        new OA\Property(property: "maxGuest", type: "integer", example: 50),
                        new OA\Property(property: "createdAt", type: "string", format: "date-time"),
                    ]
                )
            ),
            new OA\Response(
                response: "404",
                description: "Restaurant non trouvé"
            )
        ]
    )]
    public function show(int $id): JsonResponse
    {
        $restaurant = $this->repository->findOneBy(['id' => $id]);
        if ($restaurant) {
            $responseData = $this->serializer->serialize($restaurant, 'json');

            return new JsonResponse($responseData, Response::HTTP_OK, [], true);
        }

        return new JsonResponse(null, Response::HTTP_NOT_FOUND);
    }

    #[Route('/{id}', name: 'edit', methods: 'PUT')]
    #[OA\Tag(name: "CRUD Restaurant")]
    #[OA\Put(
        path: "/api/restaurant/{id}",
        summary: "Modifier un restaurant par son identifiant",
        parameters: [new OA\Parameter(
            name: "id",
            in: "path",
            required: true,
            description: "L'identifiant du restaurant à modifier",
            schema: new OA\Schema(type: "integer", example: 3)
        )],
        requestBody: new OA\RequestBody(
            required: true,
            description: "Les données du restaurant à modifier",
            content: new OA\JsonContent(
                type: "object",
                properties: [
                    new OA\Property(property: "id", type: "integer", example: 3),
                    new OA\Property(property: "name", type: "string", example: "Nom du restaurant"),
                    new OA\Property(property: "description", type: "string", example: "Description du restaurant"),
                    new OA\Property(property: "amOpeningTime", type: "array", items: (new OA\Items(type: "string", example: "08:00"))),
                    new OA\Property(property: "pmOpeningTime", type: "array", items: (new OA\Items(type: "string", example: "12:00"))),
                    new OA\Property(property: "maxGuest", type: "integer", example: 50),
                    new OA\Property(property: "createdAt", type: "string", format: "date-time"),
                    new OA\Property(property: "updateddAt", type: "string", format: "date-time"),
                    new OA\Property(property: "owner", type: "integer", example: 1)
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: "204",
                description: "Restaurant modifié avec succès",
            ),
            new OA\Response(
                response: "404",
                description: "Restaurant non trouvé"
            )
        ]
    )]
    public function edit(int $id, Request $request): JsonResponse
    {
        $restaurant = $this->repository->findOneBy(['id' => $id]);
        if ($restaurant) {
            $restaurant = $this->serializer->deserialize($request->getContent(), Restaurant::class, 'json', [AbstractNormalizer::OBJECT_TO_POPULATE => $restaurant]);
            $restaurant->setUpdatedAt(new DateTimeImmutable());
            $restaurant->setOwner($this->getUser());

            $this->manager->flush();

            return new JsonResponse(null, Response::HTTP_NO_CONTENT);
        }

        return new JsonResponse(null, Response::HTTP_NOT_FOUND);
    }


    #[Route('/{id}', name: 'delete', methods: 'DELETE')]
    #[OA\Tag(name: "CRUD Restaurant")]
    #[OA\Delete(
        path: "/api/restaurant/{id}",
        summary: "Supprimer un restaurant par son identifiant",
        parameters: [new OA\Parameter(
            name: "id",
            in: "path",
            required: true,
            description: "L'identifiant du restaurant à supprimer",
            schema: new OA\Schema(type: "integer", example: 4)
        )],
        responses: [
            new OA\Response(
                response: "204",
                description: "Restaurant supprimé avec succès"
            ),
            new OA\Response(
                response: "404",
                description: "Restaurant non trouvé"
            )
        ]
    )]
    public function delete(int $id): JsonResponse
    {
        $restaurant = $this->repository->findOneBy(['id' => $id]);
        if ($restaurant) {
            $this->manager->remove($restaurant);
            $this->manager->flush();

            return new JsonResponse(null, Response::HTTP_NO_CONTENT);
        }

        return new JsonResponse(null, Response::HTTP_NOT_FOUND);
    }

    #[Route('s', name: 'list', methods: 'GET')]
    #[OA\Tag(name: "CRUD Restaurant")]
    #[OA\Get(
        path: "/api/restaurants",
        summary: "Afficher les restaurants",
        responses: [
            new OA\Response(
                response: "200",
                description: "Liste des restaurants trouvée",
            ),
            new OA\Response(
                response: "404",
                description: "Aucun restaurant trouvé"
            )
        ]
    )]
    public function list(): JsonResponse
    {
        $restaurants = $this->repository->findAll();
        if ($restaurants) {
            $responseData = $this->serializer->serialize($restaurants, 'json', ['groups' => 'restaurant']);
            return new JsonResponse($responseData);
        }

        return new JsonResponse(null, Response::HTTP_NOT_FOUND);
    }
}
