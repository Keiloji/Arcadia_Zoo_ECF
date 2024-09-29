<?php

namespace App\Controller;

use App\Entity\Avis;
use App\Repository\AvisRepository;
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

#[Route('/api/avis', name: 'app_api_avis_')]
class AvisController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $manager,
        private AvisRepository $repository,
        private SerializerInterface $serializer,
        private UrlGeneratorInterface $urlGenerator,
    ) {}

    #[Route('', name: 'create', methods: ['POST'])]

    /** 
     * @OA\Post(
     *     path="/api/avis",
     *     summary="Créer un Avis",
     *     @OA\RequestBody(
     *         required=true,
     *         description="Données de l'avis à créer",
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", example="Nom de l'avis"),
     *             @OA\Property(property="description", type="string", example="Description de l'avis")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Avis créé avec succès",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="name", type="string", example="Nom de l'avis"),
     *             @OA\Property(property="description", type="string", example="Description de l'avis"),
     *             @OA\Property(property="createdAt", type="string", format="date-time")
     *         )
     *     )
     * )
     */
    public function new(Request $request): JsonResponse
    {
        $avis = $this->serializer->deserialize($request->getContent(), Avis::class, 'json');
        $avis->setCreatedAt(new DateTimeImmutable());

        $this->manager->persist($avis);
        $this->manager->flush();

        $responseData = $this->serializer->serialize($avis, 'json');
        $location = $this->urlGenerator->generate(
            'app_api_avis_show',
            ['id' => $avis->getId()],
            UrlGeneratorInterface::ABSOLUTE_URL
        );

        return new JsonResponse($responseData, Response::HTTP_CREATED, ["Location" => $location], true);
    }

    #[Route('/{id}', name: 'show', methods: ['GET'])]

    /** 
     * @OA\Get(
     *     path="/api/avis/{id}",
     *     summary="Récupérer un Avis par son ID",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de l'avis",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Détails de l'avis",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="name", type="string", example="Nom de l'avis"),
     *             @OA\Property(property="description", type="string", example="Description de l'avis"),
     *             @OA\Property(property="createdAt", type="string", format="date-time")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Avis non trouvé"
     *     )
     * )
     */
    public function show(int $id): JsonResponse
    {
        $avis = $this->repository->findOneBy(['id' => $id]);

        if ($avis) {
            $responseData = $this->serializer->serialize($avis, 'json');

            return new JsonResponse($responseData, Response::HTTP_OK, [], true);
        }

        return new JsonResponse(null, Response::HTTP_NOT_FOUND);
    }

    #[Route('/{id}', name: 'edit', methods: ['PUT'])]

    /** 
     * @OA\Put(
     *     path="/api/avis/{id}",
     *     summary="Mettre à jour un Avis par son ID",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de l'avis à mettre à jour",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="name", type="string", example="Nom de l'avis"),
     *             @OA\Property(property="description", type="string", example="Description de l'avis")
     *         )
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Avis mis à jour avec succès"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Avis non trouvé"
     *     )
     * )
     */
    public function edit(int $id, Request $request): JsonResponse
    {
        $avis = $this->repository->findOneBy(['id' => $id]);

        if ($avis) {
            $this->serializer->deserialize(
                $request->getContent(),
                Avis::class,
                'json',
                [AbstractNormalizer::OBJECT_TO_POPULATE => $avis]
            );
            $avis->setUpdatedAt(new DateTimeImmutable());

            $this->manager->flush();

            return new JsonResponse(null, Response::HTTP_NO_CONTENT);
        }

        return new JsonResponse(null, Response::HTTP_NOT_FOUND);
    }

    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]

    /** 
     * @OA\Delete(
     *     path="/api/avis/{id}",
     *     summary="Supprimer un Avis par son ID",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de l'avis à supprimer",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Avis supprimé avec succès"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Avis non trouvé"
     *     )
     * )
     */
    public function delete(int $id): JsonResponse
    {
        $avis = $this->repository->findOneBy(['id' => $id]);

        if ($avis) {
            $this->manager->remove($avis);
            $this->manager->flush();

            return new JsonResponse(null, Response::HTTP_NO_CONTENT);
        }

        return new JsonResponse(null, Response::HTTP_NOT_FOUND);
    }
}