<?php

namespace App\Controller;

use App\Entity\Role;
use App\Repository\RoleRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use OpenApi\Annotations as OA;
use App\Service\JwtService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/api/Role', name: 'app_api_Role_')]
class RoleController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $manager,
        private RoleRepository $repository,
        private SerializerInterface $serializer,
        private UrlGeneratorInterface $urlGenerator
    ) {
    }

    #[Route(methods: 'POST')]
    /**
     * @OA\Post(
     *     path="/api/Role",
     *     summary="Créer un nouveau rôle",
     *     @OA\RequestBody(
     *         required=true,
     *         description="Détails du rôle à créer",
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", example="Nom du rôle"),
     *             @OA\Property(property="description", type="string", example="Description du rôle")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Rôle créé avec succès",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="name", type="string", example="Nom du rôle"),
     *             @OA\Property(property="description", type="string", example="Description du rôle"),
     *             @OA\Property(property="createdAt", type="string", format="date-time")
     *         )
     *     )
     * )
     */
    public function new(Request $request): JsonResponse
    {
        $role = $this->serializer->deserialize($request->getContent(), Role::class, 'json');
        $role->setCreatedAt(new DateTimeImmutable());

        $this->manager->persist($role);
        $this->manager->flush();

        $responseData = $this->serializer->serialize($role, 'json');
        $location = $this->urlGenerator->generate(
            'app_api_Role_show',
            ['id' => $role->getId()],
            UrlGeneratorInterface::ABSOLUTE_URL
        );

        return new JsonResponse($responseData, Response::HTTP_CREATED, ["Location" => $location], true);
    }

    #[Route('/{id}', name: 'show', methods: 'GET')]
    /**
     * @OA\Get(
     *     path="/api/Role/{id}",
     *     summary="Afficher un rôle par son ID",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID du rôle à afficher",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Rôle trouvé",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="name", type="string", example="Nom du rôle"),
     *             @OA\Property(property="description", type="string", example="Description du rôle"),
     *             @OA\Property(property="createdAt", type="string", format="date-time")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Rôle non trouvé"
     *     )
     * )
     */
    public function show(int $id): JsonResponse
    {
        $role = $this->repository->findOneBy(['id' => $id]);

        if ($role) {
            $responseData = $this->serializer->serialize($role, 'json');
            return new JsonResponse($responseData, Response::HTTP_OK, [], true);
        }

        return new JsonResponse(null, Response::HTTP_NOT_FOUND);
    }

    #[Route('/{id}', name: 'edit', methods: 'PUT')]
    /**
     * @OA\Put(
     *     path="/api/Role/{id}",
     *     summary="Mettre à jour un rôle par son ID",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID du rôle à mettre à jour",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="name", type="string", example="Nom du rôle"),
     *             @OA\Property(property="description", type="string", example="Description du rôle")
     *         )
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Rôle mis à jour avec succès"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Rôle non trouvé"
     *     )
     * )
     */
    public function edit(int $id, Request $request): JsonResponse
    {
        $role = $this->repository->findOneBy(['id' => $id]);

        if ($role) {
            $role = $this->serializer->deserialize(
                $request->getContent(),
                Role::class,
                'json',
                [AbstractNormalizer::OBJECT_TO_POPULATE => $role]
            );
            $role->setUpdatedAt(new DateTimeImmutable());

            $this->manager->flush();

            return new JsonResponse(null, Response::HTTP_NO_CONTENT);
        }

        return new JsonResponse(null, Response::HTTP_NOT_FOUND);
    }

    #[Route('/{id}', name: 'delete', methods: 'DELETE')]
    /**
     * @OA\Delete(
     *     path="/api/Role/{id}",
     *     summary="Supprimer un rôle par son ID",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID du rôle à supprimer",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Rôle supprimé avec succès"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Rôle non trouvé"
     *     )
     * )
     */
    public function delete(int $id): JsonResponse
    {
        $role = $this->repository->findOneBy(['id' => $id]);

        if ($role) {
            $this->manager->remove($role);
            $this->manager->flush();

            return new JsonResponse(null, Response::HTTP_NO_CONTENT);
        }

        return new JsonResponse(null, Response::HTTP_NOT_FOUND);
    }
}