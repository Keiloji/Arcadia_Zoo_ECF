<?php

namespace App\Controller;

use App\Entity\Race;
use App\Repository\RaceRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\JwtService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use OpenApi\Annotations as OA;

#[Route('/api/Race', name: 'app_api_Race_')]
class RaceController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $manager, 
        private RaceRepository $repository,
        private SerializerInterface $serializer,
        private UrlGeneratorInterface $urlGenerator
    ) {}

    #[Route(methods: ['POST'])]
    /**
     * @OA\Post(
     *     path="/api/Race",
     *     summary="Créer un Race",
     *     @OA\RequestBody(
     *         required=true,
     *         description="Données du Race à créer",
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", example="Nom du Race"),
     *             @OA\Property(property="description", type="string", example="Description du Race")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Race créé avec succès",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="name", type="string", example="Nom du Race"),
     *             @OA\Property(property="description", type="string", example="Description du Race"),
     *             @OA\Property(property="createdAt", type="string", format="date-time")
     *         )
     *     )
     * )
     */
    public function new(Request $request): JsonResponse
    {
        $race = $this->serializer->deserialize($request->getContent(), Race::class, 'json');
        $race->setCreatedAt(new DateTimeImmutable());

        $this->manager->persist($race);
        $this->manager->flush();

        $responseData = $this->serializer->serialize($race, 'json');
        $location = $this->urlGenerator->generate('app_api_Race_show', ['id' => $race->getId()], UrlGeneratorInterface::ABSOLUTE_URL);

        return new JsonResponse($responseData, Response::HTTP_CREATED, ["Location" => $location], true);
    }

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    /**
     * @OA\Get(
     *     path="/api/Race/{id}",
     *     summary="Afficher les détails d'un Race par son ID",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID du Race",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Détails du Race",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="name", type="string", example="Nom du Race"),
     *             @OA\Property(property="description", type="string", example="Description du Race"),
     *             @OA\Property(property="createdAt", type="string", format="date-time")
     *         )
     *     ),
     *     @OA\Response(response=404, description="Race non trouvé")
     * )
     */
    public function show(int $id): JsonResponse
    {
        $race = $this->repository->findOneBy(['id' => $id]);

        if ($race) {
            $responseData = $this->serializer->serialize($race, 'json');
            return new JsonResponse($responseData, Response::HTTP_OK, [], true);
        }

        return new JsonResponse(null, Response::HTTP_NOT_FOUND);
    }

    #[Route('/{id}', name: 'edit', methods: ['PUT'])]
    /**
     * @OA\Put(
     *     path="/api/Race/{id}",
     *     summary="Mettre à jour un Race par son ID",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID du Race à mettre à jour",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="name", type="string", example="Nom du Race"),
     *             @OA\Property(property="description", type="string", example="Description du Race")
     *         )
     *     ),
     *     @OA\Response(response=204, description="Race mis à jour avec succès"),
     *     @OA\Response(response=404, description="Race non trouvé")
     * )
     */
    public function edit(int $id, Request $request): JsonResponse
    {
        $race = $this->repository->findOneBy(['id' => $id]);

        if ($race) {
            $race = $this->serializer->deserialize(
                $request->getContent(),
                Race::class,
                'json',
                [AbstractNormalizer::OBJECT_TO_POPULATE => $race]
            );
            $race->setUpdatedAt(new DateTimeImmutable());

            $this->manager->flush();

            return new JsonResponse(null, Response::HTTP_NO_CONTENT);
        }

        return new JsonResponse(null, Response::HTTP_NOT_FOUND);
    }

    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    /**
     * @OA\Delete(
     *     path="/api/Race/{id}",
     *     summary="Supprimer un Race par son ID",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID du Race à supprimer",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=204, description="Race supprimé avec succès"),
     *     @OA\Response(response=404, description="Race non trouvé")
     * )
     */
    public function delete(int $id): JsonResponse
    {
        $race = $this->repository->findOneBy(['id' => $id]);

        if ($race) {
            $this->manager->remove($race);
            $this->manager->flush();

            return new JsonResponse(null, Response::HTTP_NO_CONTENT);
        }

        return new JsonResponse(null, Response::HTTP_NOT_FOUND);
    }
}