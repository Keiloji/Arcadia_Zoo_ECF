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

#[Route('/api/user', name: 'app_api_user_')]
class UserController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $manager,
        private UserRepository $repository,
        private SerializerInterface $serializer,
    ) {}

    #[Route(methods: ['POST'])]
    /**
     * @OA\Post(
     *     path="/api/user",
     *     summary="Créer un nouvel utilisateur",
     *     @OA\RequestBody(
     *         required=true,
     *         description="Détails de l'utilisateur à créer",
     *         @OA\JsonContent(
     *             @OA\Property(property="email", type="string", example="user@example.com"),
     *             @OA\Property(property="password", type="string", example="password123"),
     *             @OA\Property(property="roles", type="array", @OA\Items(type="string"), example={"ROLE_USER"}),
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Utilisateur créé avec succès",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="email", type="string", example="user@example.com"),
     *             @OA\Property(property="roles", type="array", @OA\Items(type="string"), example={"ROLE_USER"}),
     *             @OA\Property(property="createdAt", type="string", format="date-time"),
     *         )
     *     )
     * )
     */
    public function new(Request $request): JsonResponse
    {
        // Désérialiser les données reçues dans l'objet User
        $user = $this->serializer->deserialize($request->getContent(), User::class, 'json');
        
        // Vérification des champs requis
        if (!$user->getEmail() || !$user->getPassword()) {
            return new JsonResponse(['error' => 'Email and password are required.'], Response::HTTP_BAD_REQUEST);
        }

        $user->setCreatedAt(new DateTimeImmutable());
        $user->setRoles($user->getRoles() ?: ['ROLE_USER']); // Assigner le rôle par défaut

        // Persister l'utilisateur dans la base de données
        $this->manager->persist($user);
        $this->manager->flush();

        // Préparer la réponse
        $responseData = $this->serializer->serialize($user, 'json');
        $location = $this->generateUrl('app_api_user_show', ['id' => $user->getId()], false);

        return new JsonResponse($responseData, Response::HTTP_CREATED, ["Location" => $location], true);
    }

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    /**
     * @OA\Get(
     *     path="/api/user/{id}",
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
     *             @OA\Property(property="roles", type="array", @OA\Items(type="string"), example={"ROLE_USER"}),
     *             @OA\Property(property="createdAt", type="string", format="date-time"),
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
        $user = $this->repository->findOneBy(['id' => $id]);

        if ($user) {
            $responseData = $this->serializer->serialize($user, 'json');
            return new JsonResponse($responseData, Response::HTTP_OK, [], true);
        }

        return new JsonResponse(data: null, status: Response::HTTP_NOT_FOUND);
    }

    #[Route('/{id}', name: 'edit', methods: ['PUT'])]
    /**
     * @OA\Put(
     *     path="/api/user/{id}",
     *     summary="Mettre à jour un utilisateur par son ID",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de l'utilisateur à mettre à jour",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="email", type="string", example="newuser@example.com"),
     *             @OA\Property(property="roles", type="array", @OA\Items(type="string"), example={"ROLE_USER"}),
     *         )
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Utilisateur mis à jour avec succès"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Utilisateur non trouvé"
     *     )
     * )
     */
    public function edit(int $id, Request $request): JsonResponse
    {
        $user = $this->repository->findOneBy(['id' => $id]);

        if ($user) {
            $updatedUser = $this->serializer->deserialize(
                $request->getContent(),
                User::class,
                'json',
                [AbstractNormalizer::OBJECT_TO_POPULATE => $user]
            );
            $updatedUser->setUpdatedAt(new DateTimeImmutable());

            $this->manager->flush();

            return new JsonResponse(data: null, status: Response::HTTP_NO_CONTENT);
        }

        return new JsonResponse(data: null, status: Response::HTTP_NOT_FOUND);
    }

    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    /**
     * @OA\Delete(
     *     path="/api/user/{id}",
     *     summary="Supprimer un utilisateur par son ID",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de l'utilisateur à supprimer",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Utilisateur supprimé avec succès"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Utilisateur non trouvé"
     *     )
     * )
     */
    public function delete(int $id): JsonResponse
    {
        $user = $this->repository->findOneBy(['id' => $id]);

        if ($user) {
            $this->manager->remove($user);
            $this->manager->flush();

            return new JsonResponse(data: null, status: Response::HTTP_NO_CONTENT);
        }

        return new JsonResponse(data: null, status: Response::HTTP_NOT_FOUND);
    }
}
