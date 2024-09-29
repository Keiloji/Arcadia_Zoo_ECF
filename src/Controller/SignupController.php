<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use OpenApi\Annotations as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface; // Utiliser UserPasswordHasherInterface

#[Route('/api/signup', name: 'app_api_signup_')]
class SignupController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $manager, 
        private UserRepository $repository,
        private SerializerInterface $serializer,
        private UserPasswordHasherInterface $passwordHasher // Utiliser UserPasswordHasherInterface
    ) {}

    #[Route(methods: ['POST'])]
    /**
     * @OA\Post(
     *     path="/api/signup",
     *     summary="Inscription d'un nouvel utilisateur",
     *     @OA\RequestBody(
     *         required=true,
     *         description="Détails de l'utilisateur à inscrire",
     *         @OA\JsonContent(
     *             @OA\Property(property="email", type="string", example="user@example.com"),
     *             @OA\Property(property="password", type="string", example="password123"),
     *             @OA\Property(property="roles", type="array", @OA\Items(type="string", example="ROLE_USER"))
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Utilisateur créé avec succès",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="email", type="string", example="user@example.com"),
     *             @OA\Property(property="createdAt", type="string", format="date-time")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Requête invalide"
     *     )
     * )
     */
    public function register(Request $request): JsonResponse
{
    // Désérialiser les données reçues dans l'objet User
    $user = $this->serializer->deserialize($request->getContent(), User::class, 'json');

    // Encoder le mot de passe avant de le stocker
    $user->setPassword($this->passwordHasher->hashPassword($user, $user->getPassword())); // Changer la méthode pour utiliser hashPassword
    $user->setCreatedAt(new DateTimeImmutable());
    $user->setRoles($user->getRoles() ?: ['ROLE_USER']); // Assigner le rôle utilisateur par défaut

    // Persister l'utilisateur dans la base de données
    $this->manager->persist($user);
    $this->manager->flush();

    // Préparer la réponse
    $responseData = $this->serializer->serialize($user, 'json');
    $location = $this->generateUrl('app_api_signup_show', ['id' => $user->getId()], false); // Utilisez false au lieu de absolute: true

    return new JsonResponse($responseData, Response::HTTP_CREATED, ["Location" => $location], true);
}

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    /**
     * @OA\Get(
     *     path="/api/signup/{id}",
     *     summary="Afficher un utilisateur par son ID",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de l'utilisateur à afficher",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Utilisateur trouvé avec succès",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="email", type="string", example="user@example.com"),
     *             @OA\Property(property="createdAt", type="string", format="date-time")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Utilisateur non trouvé"
     *     )
     * )
     */
    public function show(int $id): JsonResponse
    {
        $user = $this->repository->find($id);

        if ($user) {
            $responseData = $this->serializer->serialize($user, 'json');
            return new JsonResponse($responseData, Response::HTTP_OK, [], true);
        }

        return new JsonResponse(data: null, status: Response::HTTP_NOT_FOUND);
    }
}
