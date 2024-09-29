<?php

namespace App\Controller;

use App\Entity\User; // Assurez-vous que vous avez une entité User
use App\Repository\UserRepository; // Le repository de User
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use OpenApi\Annotations as OA;
use App\Service\JwtService; // Service pour gérer le JWT
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/api/signin', name:'app_api_signin_')]
class SigninController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $manager, 
        private UserRepository $repository,
        private SerializerInterface $serializer,
        private JwtService $jwtService // Injection du service JWT
    ) {}

    #[Route('', name: 'login', methods: ['POST'])]
    /**
     * @OA\Post(
     *     path="/api/signin",
     *     summary="Connexion d'un utilisateur",
     *     @OA\RequestBody(
     *         required=true,
     *         description="Données de connexion de l'utilisateur",
     *         @OA\JsonContent(
     *             @OA\Property(property="username", type="string", example="user@example.com"),
     *             @OA\Property(property="password", type="string", example="MotDePasseSecret")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Connexion réussie",
     *         @OA\JsonContent(
     *             @OA\Property(property="token", type="string", example="eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9..."),
     *             @OA\Property(property="user", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="username", type="string", example="user@example.com")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Identifiants invalides"
     *     )
     * )
     */
    public function login(Request $request): JsonResponse
    {
        // Désérialisation des données de la requête
        $data = json_decode($request->getContent(), true);
        $username = $data['username'] ?? '';
        $password = $data['password'] ?? '';

        // Validation des identifiants
        $user = $this->repository->findOneBy(['username' => $username]);
        if (!$user || !password_verify($password, $user->getPassword())) {
            return new JsonResponse(['message' => 'Identifiants invalides'], Response::HTTP_UNAUTHORIZED);
        }

        // Génération du token JWT
        $token = $this->jwtService->generateToken(['username' => $user->getUserIdentifier()]);

        // Réponse avec le token et les informations de l'utilisateur
        return new JsonResponse([
            'token' => $token,
            'user' => [
                'id' => $user->getId(),
                'username' => $user->getUserIdentifier(),
            ]
        ], Response::HTTP_OK);
    }
}
