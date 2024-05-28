<?php

namespace App\Controller;

use OA\property;
use OA\RequestBody;
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
                'use' => $user->getUserIdentifier(),
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
                    new OA\Property(property: "username", type: "string", example: "adresse@mail.Com"),
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
                    new OA\Property(property: "user", type: "string", example: "adresse@mail.Com"),
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
}
