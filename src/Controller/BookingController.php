<?php

namespace App\Controller;

use DateTimeImmutable;
use App\Entity\Booking;
use OpenApi\Attributes as OA;
use App\Repository\BookingRepository;
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

#[Route('api/restaurant/{id}/booking', name: 'app_api_restaurant_booking_')]
class BookingController extends AbstractController
{
    public function __construct(private EntityManagerInterface $manager, private BookingRepository $repository, private RestaurantRepository $restaurantRepository, private SerializerInterface $serializer, private UrlGeneratorInterface $urlGenerator)
    {
    }

    #[Route(methods: 'POST')]
    #[OA\Tag(name: "CRUD Booking")]
    #[OA\Post(
        path: "/api/restaurant/{id}/booking",
        summary: "Créer une réservation",
        parameters: [new OA\Parameter(
            name: "id",
            in: "path",
            required: true,
            description: "L'identifiant du restaurant",
            schema: new OA\Schema(type: "integer", example: 1)
        )],
        requestBody: new OA\RequestBody(
            required: true,
            description: "Les données de la réservation à créer",
            content: new OA\JsonContent(
                type: "object",
                properties: [
                    new OA\Property(property: "guestNumber", type: "integer", example: 2),
                    new OA\Property(property: "orderDate", type: "string", format: "date", example: "31-12-2022"),
                    new OA\Property(property: "orderHour", type: "string", format: "date-time", example: "20:00"),
                    new OA\Property(property: "allergy", type: "string", example: "Aucune allergie"),
                    new OA\Property(property: "restaurant", type: "integer", example: 1),
                    new OA\Property(property: "client", type: "integer", example: 1)
                ]
            )
        ),
        responses: [new OA\Response(
            response: "201",
            description: "Réservation créée avec succès",
            content: new OA\JsonContent(
                type: "object",
                properties: [
                    new OA\Property(property: "id", type: "integer", example: 1),
                    new OA\Property(property: "guestNumber", type: "integer", example: 2),
                    new OA\Property(property: "orderDate", type: "string", format: "date", example: "31-12-2022"),
                    new OA\Property(property: "orderHour", type: "string", format: "date-time", example: "20:00"),
                    new OA\Property(property: "allergy", type: "string", example: "Aucune allergie"),
                    new OA\Property(property: "createdAt", type: "string", format: "date-time"),
                    new OA\Property(property: "restaurant", type: "integer", example: 1),
                    new OA\Property(property: "client", type: "integer", example: 1)
                ]
            )
        )]
    )]
    public function new(Request $request, int $id): JsonResponse
    {
        $booking = $this->serializer->deserialize($request->getContent(), Booking::class, 'json');

        $restaurant = $this->restaurantRepository->find($id);

        if ($restaurant) {
            $booking->setRestaurant($restaurant);
            $booking->setCreatedAt(new DateTimeImmutable());
            $booking->setClient($this->getUser());

            $this->manager->persist($booking);
            $this->manager->flush();

            $responseData = $this->serializer->serialize($booking, 'json', [AbstractNormalizer::IGNORED_ATTRIBUTES => ['restaurant', 'client']]);
            $location = $this->urlGenerator->generate('app_api_restaurant_booking_show', ['id' => $restaurant->getId(), 'bookingId' => $booking->getId()], UrlGeneratorInterface::ABSOLUTE_URL);

            return new JsonResponse($responseData, Response::HTTP_CREATED, ['Location' => $location], true);
        }

        return new JsonResponse(null, Response::HTTP_NOT_FOUND);
    }

    #[Route('/{bookingId}', name: 'show', methods: 'GET')]
    #[OA\Tag(name: "CRUD Booking")]
    #[OA\Get(
        path: "/api/restaurant/{id}/booking/{bookingId}",
        summary: "Afficher une réservation",
        parameters: [new OA\Parameter(
            name: "id",
            in: "path",
            required: true,
            description: "L'identifiant du restaurant",
            schema: new OA\Schema(type: "integer", example: 1)
        ), new OA\Parameter(
            name: "bookingId",
            in: "path",
            required: true,
            description: "L'identifiant de la réservation",
            schema: new OA\Schema(type: "integer", example: 1)
        )],
        responses: [new OA\Response(
            response: "200",
            description: "Réservation affichée avec succès",
            content: new OA\JsonContent(
                type: "object",
                properties: [
                    new OA\Property(property: "id", type: "integer", example: 1),
                    new OA\Property(property: "guestNumber", type: "integer", example: 2),
                    new OA\Property(property: "orderDate", type: "string", format: "date", example: "31-12-2022"),
                    new OA\Property(property: "orderHour", type: "string", format: "date-time", example: "20:00"),
                    new OA\Property(property: "allergy", type: "string", example: "Aucune allergie"),
                    new OA\Property(property: "createdAt", type: "string", format: "date-time"),
                    new OA\Property(property: "restaurant", type: "integer", example: 1),
                    new OA\Property(property: "client", type: "integer", example: 1)
                ]
            )
        )]
    )]
    public function show(int $id, int $bookingId): JsonResponse
    {
        $booking = $this->repository->findOneBy(['id' => $bookingId, 'restaurant' => $id]);

        if ($booking) {
            $responseData = $this->serializer->serialize($booking, 'json', [AbstractNormalizer::IGNORED_ATTRIBUTES => ['restaurant', 'client']]);

            return new JsonResponse($responseData);
        }

        return new JsonResponse(null, Response::HTTP_NOT_FOUND);
    }

    #[Route('/{bookingId}', name: 'edit', methods: 'PUT')]
    #[OA\Tag(name: "CRUD Booking")]
    #[OA\Put(
        path: "/api/restaurant/{id}/booking/{bookingId}",
        summary: "Modifier une réservation",
        parameters: [new OA\Parameter(
            name: "id",
            in: "path",
            required: true,
            description: "L'identifiant du restaurant",
            schema: new OA\Schema(type: "integer", example: 1)
        ), new OA\Parameter(
            name: "bookingId",
            in: "path",
            required: true,
            description: "L'identifiant de la réservation",
            schema: new OA\Schema(type: "integer", example: 1)
        )],
        requestBody: new OA\RequestBody(
            required: true,
            description: "Les données de la réservation à modifier",
            content: new OA\JsonContent(
                type: "object",
                properties: [
                    new OA\Property(property: "guestNumber", type: "integer", example: 2),
                    new OA\Property(property: "orderDate", type: "string", format: "date", example: "31-12-2022"),
                    new OA\Property(property: "orderHour", type: "string", format: "date-time", example: "20:00"),
                    new OA\Property(property: "allergy", type: "string", example: "Aucune allergie"),
                    new OA\Property(property: "restaurant", type: "integer", example: 1),
                    new OA\Property(property: "client", type: "integer", example: 1)
                ]
            )
        ),
        responses: [new OA\Response(
            response: "204",
            description: "Réservation modifiée avec succès",
        ), new OA\Response(
            response: "404",
            description: "Réservation non trouvée"
        )]
    )]
    public function edit(Request $request, int $id, int $bookingId): JsonResponse
    {
        $booking = $this->repository->findOneBy(['id' => $bookingId, 'restaurant' => $id]);

        if ($booking) {
            $this->serializer->deserialize($request->getContent(), Booking::class, 'json', [AbstractNormalizer::OBJECT_TO_POPULATE => $booking]);
            $booking->setUpdatedAt(new DateTimeImmutable());
            $booking->setClient($this->getUser());
            $booking->setRestaurant($this->restaurantRepository->find($id));

            $this->manager->flush();

            return new JsonResponse(null, Response::HTTP_NO_CONTENT);
        }

        return new JsonResponse(null, Response::HTTP_NOT_FOUND);
    }

    #[Route('/{bookingId}', name: 'delete', methods: 'DELETE')]
    #[OA\Tag(name: "CRUD Booking")]
    #[OA\Delete(
        path: "/api/restaurant/{id}/booking/{bookingId}",
        summary: "Supprimer une réservation",
        parameters: [new OA\Parameter(
            name: "id",
            in: "path",
            required: true,
            description: "L'identifiant du restaurant",
            schema: new OA\Schema(type: "integer", example: 1)
        ), new OA\Parameter(
            name: "bookingId",
            in: "path",
            required: true,
            description: "L'identifiant de la réservation",
            schema: new OA\Schema(type: "integer", example: 1)
        )],
        responses: [new OA\Response(
            response: "204",
            description: "Réservation supprimée avec succès"
        )]
    )]
    public function delete(int $id, int $bookingId): JsonResponse
    {
        $booking = $this->repository->findOneBy(['id' => $bookingId, 'restaurant' => $id]);

        if ($booking) {
            $this->manager->remove($booking);
            $this->manager->flush();

            return new JsonResponse(null, Response::HTTP_NO_CONTENT);
        }

        return new JsonResponse(null, Response::HTTP_NOT_FOUND);
    }

    #[Route('s', name: 'list', methods: 'GET')]
    #[OA\Tag(name: "CRUD Booking")]
    #[OA\Get(
        path: "/api/restaurant/{id}/bookings",
        summary: "Afficher toutes les réservations",
        parameters: [new OA\Parameter(
            name: "id",
            in: "path",
            required: true,
            description: "L'identifiant du restaurant",
            schema: new OA\Schema(type: "integer", example: 1)
        )],
        responses: [new OA\Response(
            response: "200",
            description: "Réservations affichées avec succès",
        ), new OA\Response(
            response: "404",
            description: "Aucune réservation trouvée"
        )]
    )]
    public function list(int $id): JsonResponse
    {
        $bookings = $this->repository->findBy(['restaurant' => $id]);

        if ($bookings) {
            $responseData = $this->serializer->serialize($bookings, 'json', [AbstractNormalizer::IGNORED_ATTRIBUTES => ['restaurant', 'client']]);

            return new JsonResponse($responseData);
        }

        return new JsonResponse(null, Response::HTTP_NOT_FOUND);
    }
}
