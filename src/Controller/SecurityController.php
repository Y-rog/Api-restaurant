<?php

namespace App\Controller;

use App\Entity\User;
use OpenApi\Attributes as OA;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[Route('/api', name: 'app_api_')]
class SecurityController extends AbstractController
{
    public function __construct(private EntityManagerInterface $manager, private SerializerInterface $serializer)
    {
    }

    #[Route('/register', name: 'register', methods: ['POST'])]
    #[OA\Tag(name: "Inscription")]
    #[OA\Post(
        path: "/api/register",
        summary: "Inscription d\'un nouvel utilisateur",
        requestBody: new OA\RequestBody(
            required: true,
            description: "Les données de l'utilisateur à inscrire",
            content: new OA\JsonContent(
                type: "object",
                properties: [
                    new OA\Property(property: "firstName", type: "string", example: "firstName"),
                    new OA\Property(property: "lastName", type: "string", example: "lastName"),
                    new OA\Property(property: "email", type: "string", example: "adresse@mail.Com"),
                    new OA\Property(property: "password", type: "string", example: "Mot de passe"),
                    new OA\Property(property: "roles", type: "array", items: (new OA\Items(type: "string",      example: "ROLE_USER"))),
                    new OA\Property(property: "guestNumber", type: "integer", example: 1),
                ]
            )
        ),
        responses: [new OA\Response(
            response: "201",
            description: "Utilisateur inscrit avec succès",
            content: new OA\JsonContent(
                type: "object",
                properties: [
                    new OA\Property(property: "user", type: "string", example: "adresse@mail.Com"),
                    new OA\Property(property: "apiToken", type: "string", example: "cs<ce5ce15ce1q1e5c1e5cec5e6ce26ce6ce2ce6c2e6c2ec65e5c1ec51ec"),
                    new OA\Property(property: "roles", type: "array", items: (new OA\Items(type: "string",        example: "ROLE_USER"))),
                ]
            )
        )]
    )]
    public function register(Request $request, UserPasswordHasherInterface $passwordHasher): JsonResponse
    {
        $user = $this->serializer->deserialize($request->getContent(), User::class, 'json');
        $user->setPassword($passwordHasher->hashPassword($user, $user->getPassword()));
        $user->setCreatedAt(new \DateTimeImmutable());

        $this->manager->persist($user);
        $this->manager->flush();

        return new JsonResponse(
            [
                'user' => $user->getUserIdentifier(),
                'apiToken' => $user->getApiToken(),
                'roles' => $user->getRoles(),
            ],
            Response::HTTP_CREATED
        );
    }

    #[Route('/login', name: 'login', methods: ['POST'])]
    #[OA\Tag(name: "Connexion")]
    #[OA\Post(
        path: "/api/login",
        summary: "Connecter un utilisateur",
        requestBody: new OA\RequestBody(
            required: true,
            description: "Les données de l'utilisateur à inscrire",
            content: new OA\JsonContent(
                type: "object",
                properties: [
                    new OA\Property(property: "username", type: "string", example: "adresse@mail.com"),
                    new OA\Property(property: "password", type: "string", example: "Mot de passe"),
                ]
            )
        ),
        responses: [new OA\Response(
            response: "200",
            description: "Conneixion réussie",
            content: new OA\JsonContent(
                type: "object",
                properties: [
                    new OA\Property(property: "user", type: "string", example: "adresse@mail.com"),
                    new OA\Property(property: "apiToken", type: "string", example: "cs<ce5ce15ce1q1e5c1e5cec5e6ce26ce6ce2ce6c2e6c2ec65e5c1ec51ec"),
                    new OA\Property(property: "roles", type: "array", items: (new OA\Items(type: "string",        example: "ROLE_USER"))),
                ]
            )
        )]
    )]
    public function login(#[CurrentUser] ?user $user): JsonResponse
    {
        if (null === $user) {
            return new JsonResponse(
                ['messaage' => 'missing credentials'],
                Response::HTTP_UNAUTHORIZED
            );
        }

        return new JsonResponse(
            [
                'use' => $user->getUserIdentifier(),
                'apiToken' => $user->getApiToken(),
                'roles' => $user->getRoles(),
            ]
        );
    }

    #[Route('/logout', name: 'logout', methods: ['POST'])]
    #[OA\Tag(name: "Déconnexion")]
    #[OA\Post(
        path: "/api/logout",
        summary: "Déconnecter un utilisateur",
        responses: [new OA\Response(
            response: "200",
            description: "Déconnexion réussie",
            content: new OA\JsonContent(
                type: "object",
                properties: [
                    new OA\Property(property: "message", type: "string", example: "Déconnexion réussie"),
                ]
            )
        )]
    )]
    public function logout(): JsonResponse
    {
        $this->getUser()->eraseCredentials();
        return new JsonResponse(
            ['message' => 'logout successful'],
            Response::HTTP_OK
        );
    }

    #[Route('/account/me', name: 'me', methods: ['GET'])]
    #[OA\Tag(name: "Compte")]
    #[OA\Get(
        path: "/api/account/me",
        summary: "Récupérer les informations de l'utilisateur connecté",
        responses: [new OA\Response(
            response: "200",
            description: "Informations de l'utilisateur connecté",
            content: new OA\JsonContent(
                type: "object",
                properties: [
                    new OA\Property(property: "id", type: "integer", example: 1),
                    new OA\Property(property: "firstName", type: "string", example: "firstName"),
                    new OA\Property(property: "lastName", type: "string", example: "lastName"),
                    new OA\Property(property: "password", type: "string", example: "Mot de passe"),
                    new OA\Property(property: "roles", type: "array", items: (new OA\Items(type: "string", example: "ROLE_USER"))),
                    new OA\Property(property: "guestNumber", type: "integer", example: 1),
                    new OA\Property(property: "allergy", type: "string", example: "allergy"),
                    new OA\Property(property: "createdAt", type: "string", example: "2021-09-01 12:00:00"),
                    new OA\Property(property: "updatedAt", type: "string", example: "2021-09-01 12:00:00"),
                    new OA\Property(property: "user", type: "string", example: "adresse@mail.com"),
                ]
            )
        )]
    )]
    public function me(#[CurrentUser] ?user $user): JsonResponse
    {
        if ($user) {
            return new JsonResponse(
                [
                    'id' => $user->getId(),
                    'firstName' => $user->getFirstName(),
                    'lastName' => $user->getLastName(),
                    'password' => $user->getPassword(),
                    'roles' => $user->getRoles(),
                    'guestNumber' => $user->getGuestNumber(),
                    'allergy' => $user->getAllergy(),
                    'createdAt' => $user->getCreatedAt(),
                    'updatedAt' => $user->getUpdatedAt(),
                    'email' => $user->getEmail(),
                ]
            );
        }
        return new JsonResponse(
            ['message' => 'user not found'],
            Response::HTTP_NOT_FOUND
        );
    }

    #[Route('/account/edit', name: 'edit', methods: ['PUT'])]
    #[OA\Tag(name: "Compte")]
    #[OA\Put(
        path: "/api/account/edit",
        summary: "Modifier les informations de l'utilisateur connecté",
        requestBody: new OA\RequestBody(
            required: true,
            description: "Les données de l'utilisateur à modifier",
            content: new OA\JsonContent(
                type: "object",
                properties: [
                    new OA\Property(property: "firstName", type: "string", example: "firstName"),
                    new OA\Property(property: "lastName", type: "string", example: "lastName"),
                    new OA\Property(property: "password", type: "string", example: "Mot de passe"),
                    new OA\Property(property: "roles", type: "array", items: (new OA\Items(type: "string", example: "ROLE_USER"))),
                    new OA\Property(property: "guestNumber", type: "integer", example: 1),
                    new OA\Property(property: "allergy", type: "string", example: "allergy"),
                    new OA\Property(property: "email", type: "string", example: "adresse@mail.com"),
                ]
            )
        ),
        responses: [new OA\Response(
            response: "200",
            description: "Les données de l'utilisateur ont été modifiées avec succès",
            content: new OA\JsonContent(
                type: "object",
                properties: [
                    new OA\Property(property: "user", type: "string", example: "adresse@mail.com"),
                    new OA\Property(property: "apiToken", type: "string", example: "cs<ce5ce15ce1q1e5c1e5cec5e6ce26ce6ce2ce6c2e6c2ec65e5c1ec51ec"),
                    new OA\Property(property: "roles", type: "array", items: (new OA\Items(type: "string", example: "ROLE_USER"))),
                ]
            )
        ), new OA\Response(
            response: "404",
            description: "Utilisateur non trouvé",
        )]
    )]
    public function edit(Request $request, UserPasswordHasherInterface $passwordHasher, #[CurrentUser] ?user $user): JsonResponse
    {
        if ($user) {
            $user = $this->serializer->deserialize($request->getContent(), User::class, 'json');
            $user->setPassword($passwordHasher->hashPassword($user, $user->getPassword()));
            $user->setUpdatedAt(new \DateTimeImmutable());

            $this->manager->flush();

            return new JsonResponse(
                [
                    'user' => $user->getUserIdentifier(),
                    'apiToken' => $user->getApiToken(),
                    'roles' => $user->getRoles(),
                ],
                Response::HTTP_OK
            );
        }
        return new JsonResponse(
            ['message' => 'user not found'],
            Response::HTTP_NOT_FOUND
        );
    }

    #[Route('/account/delete', name: 'delete', methods: ['DELETE'])]
    #[OA\Tag(name: "Compte")]
    #[OA\Delete(
        path: "/api/account/delete",
        summary: "Supprimer le compte de l'utilisateur connecté",
        responses: [new OA\Response(
            response: "200",
            description: "Le compte de l'utilisateur a été supprimé avec succès",
            content: new OA\JsonContent(
                type: "object",
                properties: [
                    new OA\Property(property: "message", type: "string", example: "Le compte de l'utilisateur a été supprimé avec succès"),
                ]
            )
        ), new OA\Response(
            response: "404",
            description: "Utilisateur non trouvé",
        )]
    )]
    public function delete(#[CurrentUser] ?user $user): JsonResponse
    {
        if ($user) {
            $this->manager->remove($user);
            $this->manager->flush();

            return new JsonResponse(
                ['message' => 'user deleted'],
                Response::HTTP_OK
            );
        }
        return new JsonResponse(
            ['message' => 'user not found'],
            Response::HTTP_NOT_FOUND
        );
    }
}
