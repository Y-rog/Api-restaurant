<?php

namespace App\Controller;

use DateTimeImmutable;
use App\Entity\Picture;
use OpenApi\Attributes as OA;
use App\Repository\PictureRepository;
use App\Repository\RestaurantRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('api/restaurant/{id}/picture', name: 'app_api_restaurant_picture_')]
class PictureController extends AbstractController
{
    public function __construct(private EntityManagerInterface $manager, private PictureRepository $repository, private RestaurantRepository $restaurantRepository, private SerializerInterface $serializer, private UrlGeneratorInterface $urlGenerator)
    {
    }

    #[Route(methods: 'POST')]
    #[OA\Tag(name: "CRUD Picture")]
    #[OA\Post(
        path: "/api/restaurant/{id}/picture",
        summary: "Créer une image",
        parameters: [new OA\Parameter(
            name: "id",
            in: "path",
            required: true,
            description: "L'identifiant du restaurant",
            schema: new OA\Schema(type: "integer", example: 1)
        )],
        requestBody: new OA\RequestBody(
            required: true,
            description: "Les données de l'image à créer",
            content: new OA\JsonContent(
                type: "object",
                properties: [
                    new OA\Property(property: "title", type: "string", example: "Image 1"),
                    new OA\Property(property: "slug", type: "string", example: "image-1"),
                    new OA\Property(property: "restaurant", type: "integer", example: 1)
                ]
            )
        ),
        responses: [new OA\Response(
            response: "201",
            description: "Image créée avec succès",
            content: new OA\JsonContent(
                type: "object",
                properties: [
                    new OA\Property(property: "title", type: "string", example: "Image 1"),
                    new OA\Property(property: "slug", type: "string", example: "image-1"),
                    new OA\Property(property: "createdAt", type: "string", format: "date-time", example: "31-12-2022 20:00"),
                    new OA\Property(property: "restaurant", type: "integer", example: 1)
                ]
            )
        )]
    )]
    public function new(Request $request, int $id): JsonResponse
    {
        $picture = $this->serializer->deserialize($request->getContent(), Picture::class, 'json');

        $restaurant = $this->restaurantRepository->find($id);

        if ($restaurant) {
            $picture->setRestaurant($restaurant);
            $picture->setCreatedAt(new DateTimeImmutable());

            $this->manager->persist($picture);
            $this->manager->flush();

            $responseData = $this->serializer->serialize($picture, 'json', [AbstractNormalizer::IGNORED_ATTRIBUTES => ['restaurant']]);
            $location = $this->urlGenerator->generate('app_api_restaurant_picture_show', ['id' => $restaurant->getId(), 'pictureId' => $picture->getId()], UrlGeneratorInterface::ABSOLUTE_URL);

            return new JsonResponse($responseData, Response::HTTP_CREATED, ['Location' => $location], true);
        }

        return new JsonResponse(null, Response::HTTP_NOT_FOUND);
    }

    #[Route('/{pictureId}', name: 'show', methods: 'GET')]
    #[OA\Tag(name: "CRUD Picture")]
    #[OA\Get(
        path: "/api/restaurant/{id}/picture/{pictureId}",
        summary: "Afficher une image",
        parameters: [new OA\Parameter(
            name: "id",
            in: "path",
            required: true,
            description: "L'image à afficher",
            schema: new OA\Schema(type: "integer", example: 1)
        ), new OA\Parameter(
            name: "pictureId",
            in: "path",
            required: true,
            description: "L'identifiant de l'image à afficher",
            schema: new OA\Schema(type: "integer", example: 1)
        )],
        responses: [new OA\Response(
            response: "200",
            description: "Image trouvée",
            content: new OA\JsonContent(
                type: "object",
                properties: [
                    new OA\Property(property: "id", type: "integer", example: 1),
                    new OA\Property(property: "title", type: "string", example: "Image 1"),
                    new OA\Property(property: "slug", type: "string", example: "image-1"),
                    new OA\Property(property: "createdAt", type: "string", format: "date-time", example: "31-12-2022 20:00"),
                    new OA\Property(property: "restaurant", type: "integer", example: 1)
                ]
            )
        )]
    )]
    public function show(int $id, int $pictureId): JsonResponse
    {
        $picture = $this->repository->findOneBy(['id' => $pictureId, 'restaurant' => $id]);

        if ($picture) {
            $responseData = $this->serializer->serialize($picture, 'json', [AbstractNormalizer::IGNORED_ATTRIBUTES => ['restaurant']]);

            return new JsonResponse($responseData);
        }

        return new JsonResponse(null, Response::HTTP_NOT_FOUND);
    }

    #[Route('/{pictureId}', name: 'edit', methods: 'PUT')]
    #[OA\Tag(name: "CRUD Picture")]
    #[OA\Put(
        path: "/api/restaurant/{id}/picture/{pictureId}",
        summary: "Modifier une image",
        parameters: [new OA\Parameter(
            name: "id",
            in: "path",
            required: true,
            description: "L'identifiant du restaurant",
            schema: new OA\Schema(type: "integer", example: 1)
        ), new OA\Parameter(
            name: "pictureId",
            in: "path",
            required: true,
            description: "L'identifiant de l'image à modifier",
            schema: new OA\Schema(type: "integer", example: 1)
        )],
        requestBody: new OA\RequestBody(
            required: true,
            description: "Les données de l'image' à modifier",
            content: new OA\JsonContent(
                type: "object",
                properties: [
                    new OA\Property(property: "title", type: "string", example: "Image 1"),
                    new OA\Property(property: "slug", type: "string", example: "image-1"),
                    new OA\Property(property: "restaurant", type: "integer", example: 1)
                ]
            )
        ),
        responses: [new OA\Response(
            response: "204",
            description: "Image modifiée avec succès",
        ), new OA\Response(
            response: "404",
            description: "Image non trouvée"
        )]
    )]
    public function edit(Request $request, int $id, int $pictureId): JsonResponse
    {
        $picture = $this->repository->findOneBy(['id' => $pictureId, 'restaurant' => $id]);

        if ($picture) {
            $this->serializer->deserialize($request->getContent(), Picture::class, 'json', [AbstractNormalizer::OBJECT_TO_POPULATE => $picture]);
            $picture->setUpdatedAt(new DateTimeImmutable());
            $picture->setRestaurant($this->restaurantRepository->find($id));

            $this->manager->flush();

            return new JsonResponse(null, Response::HTTP_NO_CONTENT);
        }

        return new JsonResponse(null, Response::HTTP_NOT_FOUND);
    }

    #[Route('/{pictureId}', name: 'delete', methods: 'DELETE')]
    #[OA\Tag(name: "CRUD Picture")]
    #[OA\Delete(
        path: "/api/restaurant/{id}/picture/{pictureId}",
        summary: "Supprimer une image",
        parameters: [new OA\Parameter(
            name: "id",
            in: "path",
            required: true,
            description: "L'identifiant du restaurant",
            schema: new OA\Schema(type: "integer", example: 1)
        ), new OA\Parameter(
            name: "pictureId",
            in: "path",
            required: true,
            description: "L'identifiant de l'image à supprimer",
            schema: new OA\Schema(type: "integer", example: 1)
        )],
        responses: [new OA\Response(
            response: "204",
            description: "Image supprimée avec succès"
        )]
    )]
    public function delete(int $id, int $pictureId): JsonResponse
    {
        $picture = $this->repository->findOneBy(['id' => $pictureId, 'restaurant' => $id]);

        if ($picture) {
            $this->manager->remove($picture);
            $this->manager->flush();

            return new JsonResponse(null, Response::HTTP_NO_CONTENT);
        }

        return new JsonResponse(null, Response::HTTP_NOT_FOUND);
    }

    #[Route('s', name: 'list', methods: 'GET')]
    #[OA\Tag(name: "CRUD Picture")]
    #[OA\Get(
        path: "/api/restaurant/{id}/pictures",
        summary: "Afficher les images d'un restaurant",
        parameters: [new OA\Parameter(
            name: "id",
            in: "path",
            required: true,
            description: "L'identifiant du restaurant",
            schema: new OA\Schema(type: "integer", example: 1)
        )],
        responses: [new OA\Response(
            response: "200",
            description: "Images trouvées",
        ), new OA\Response(
            response: "404",
            description: "Aucune image trouvée"
        )]
    )]
    public function list(int $id): JsonResponse
    {
        $pictures = $this->repository->findBy(['restaurant' => $id]);

        if ($pictures) {
            $responseData = $this->serializer->serialize($pictures, 'json', [AbstractNormalizer::IGNORED_ATTRIBUTES => ['restaurant']]);

            return new JsonResponse($responseData);
        }

        return new JsonResponse(null, Response::HTTP_NOT_FOUND);
    }
}
