<?php

namespace App\Controller;

use App\Entity\Habitat;
use App\Repository\HabitatRepository;
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

#[Route('/api/habitat', name: 'app_api_habitat_')]
class HabitatController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $manager,
        private HabitatRepository $repository,
        private SerializerInterface $serializer,
        private UrlGeneratorInterface $urlGenerator
    ) {
    }

    /**
     * @OA\Post(
     *     path="/api/habitat",
     *     summary="Créer un nouveau Habitat",
     *     @OA\RequestBody(
     *         required=true,
     *         description="Données du Habitat à créer",
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", example="Nom du Habitat"),
     *             @OA\Property(property="description", type="string", example="Description du Habitat")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Habitat créé avec succès",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="name", type="string", example="Nom du Habitat"),
     *             @OA\Property(property="description", type="string", example="Description du Habitat"),
     *             @OA\Property(property="createdAt", type="string", format="date-time")
     *         )
     *     )
     * )
     */
    #[Route(methods: ['POST'])]
    public function new(Request $request): JsonResponse
    {
        $habitat = $this->serializer->deserialize($request->getContent(), Habitat::class, 'json');
        $habitat->setCreatedAt(new DateTimeImmutable());

        $this->manager->persist($habitat);
        $this->manager->flush();

        $responseData = $this->serializer->serialize($habitat, 'json');
        $location = $this->urlGenerator->generate(
            'app_api_habitat_show',
            ['id' => $habitat->getId()],
            UrlGeneratorInterface::ABSOLUTE_URL
        );

        return new JsonResponse($responseData, Response::HTTP_CREATED, ['Location' => $location], true);
    }

    /**
     * @OA\Get(
     *     path="/api/habitat/{id}",
     *     summary="Afficher un Habitat par son ID",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID du Habitat",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Détails du Habitat",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="name", type="string", example="Nom du Habitat"),
     *             @OA\Property(property="description", type="string", example="Description du Habitat")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Habitat non trouvé"
     *     )
     * )
     */
    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(int $id): JsonResponse
    {
        $habitat = $this->repository->findOneBy(['id' => $id]);

        if ($habitat) {
            $responseData = $this->serializer->serialize($habitat, 'json');
            return new JsonResponse($responseData, Response::HTTP_OK, [], true);
        }

        return new JsonResponse(null, Response::HTTP_NOT_FOUND);
    }

    /**
     * @OA\Put(
     *     path="/api/habitat/{id}",
     *     summary="Mettre à jour un Habitat par son ID",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID du Habitat à mettre à jour",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="name", type="string", example="Nom du Habitat"),
     *             @OA\Property(property="description", type="string", example="Description du Habitat")
     *         )
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Habitat mis à jour avec succès"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Habitat non trouvé"
     *     )
     * )
     */
    #[Route('/{id}', name: 'edit', methods: ['PUT'])]
    public function edit(int $id, Request $request): JsonResponse
    {
        $habitat = $this->repository->findOneBy(['id' => $id]);

        if ($habitat) {
            $this->serializer->deserialize(
                $request->getContent(),
                Habitat::class,
                'json',
                [AbstractNormalizer::OBJECT_TO_POPULATE => $habitat]
            );
            $habitat->setUpdatedAt(new DateTimeImmutable());

            $this->manager->flush();

            return new JsonResponse(null, Response::HTTP_NO_CONTENT);
        }

        return new JsonResponse(null, Response::HTTP_NOT_FOUND);
    }

    /**
     * @OA\Delete(
     *     path="/api/habitat/{id}",
     *     summary="Supprimer un Habitat par son ID",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID du Habitat à supprimer",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Habitat supprimé avec succès"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Habitat non trouvé"
     *     )
     * )
     */
    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    public function delete(int $id): JsonResponse
    {
        $habitat = $this->repository->findOneBy(['id' => $id]);

        if ($habitat) {
            $this->manager->remove($habitat);
            $this->manager->flush();

            return new JsonResponse(null, Response::HTTP_NO_CONTENT);
        }

        return new JsonResponse(null, Response::HTTP_NOT_FOUND);
    }
}