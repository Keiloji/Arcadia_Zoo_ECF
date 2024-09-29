<?php

namespace App\Controller;

use App\Entity\RapportVeterinaire;
use App\Repository\RapportVeterinaireRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use OpenApi\Annotations as OA;
use App\Service\JwtService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/api/rapport_veterinaire', name: 'app_api_rapport_veterinaire_')]
class RapportVeterinaireController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $manager,
        private RapportVeterinaireRepository $repository,
        private SerializerInterface $serializer,
        private UrlGeneratorInterface $urlGenerator,
    ) {
    }

    #[Route(methods: ['POST'])]
    /**
     * @OA\Post(
     *     path="/api/rapport_veterinaire",
     *     summary="Créer un rapport vétérinaire",
     *     @OA\RequestBody(
     *         required=true,
     *         description="Détails du rapport vétérinaire à créer",
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", example="Nom du rapport"),
     *             @OA\Property(property="description", type="string", example="Description du rapport")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Rapport vétérinaire créé avec succès",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="name", type="string", example="Nom du rapport"),
     *             @OA\Property(property="description", type="string", example="Description du rapport"),
     *             @OA\Property(property="createdAt", type="string", format="date-time")
     *         )
     *     )
     * )
     */
    public function new(Request $request): JsonResponse
    {
        $rapport = $this->serializer->deserialize($request->getContent(), RapportVeterinaire::class, 'json');
        $rapport->setCreatedAt(new DateTimeImmutable());

        $this->manager->persist($rapport);
        $this->manager->flush();

        $responseData = $this->serializer->serialize($rapport, 'json');
        $location = $this->urlGenerator->generate(
            'app_api_rapport_veterinaire_show',
            ['id' => $rapport->getId()],
            UrlGeneratorInterface::ABSOLUTE_URL
        );

        return new JsonResponse($responseData, Response::HTTP_CREATED, ["Location" => $location], true);
    }

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    /**
     * @OA\Get(
     *     path="/api/rapport_veterinaire/{id}",
     *     summary="Afficher un rapport vétérinaire par son ID",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID du rapport vétérinaire",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Rapport trouvé",
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="name", type="string", example="Nom du rapport"),
     *             @OA\Property(property="description", type="string", example="Description du rapport"),
     *             @OA\Property(property="createdAt", type="string", format="date-time")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Rapport non trouvé"
     *     )
     * )
     */
    public function show(int $id): JsonResponse
    {
        $rapport = $this->repository->findOneBy(['id' => $id]);

        if ($rapport) {
            $responseData = $this->serializer->serialize($rapport, 'json');

            return new JsonResponse($responseData, Response::HTTP_OK, [], true);
        }

        return new JsonResponse(null, Response::HTTP_NOT_FOUND);
    }

    #[Route('/{id}', name: 'edit', methods: ['PUT'])]
    /**
     * @OA\Put(
     *     path="/api/rapport_veterinaire/{id}",
     *     summary="Mettre à jour un rapport vétérinaire par son ID",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID du rapport vétérinaire à mettre à jour",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="name", type="string", example="Nom du rapport"),
     *             @OA\Property(property="description", type="string", example="Description du rapport")
     *         )
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Rapport mis à jour avec succès"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Rapport non trouvé"
     *     )
     * )
     */
    public function edit(int $id, Request $request): JsonResponse
    {
        $rapport = $this->repository->findOneBy(['id' => $id]);

        if ($rapport) {
            $this->serializer->deserialize(
                $request->getContent(),
                RapportVeterinaire::class,
                'json',
                [AbstractNormalizer::OBJECT_TO_POPULATE => $rapport]
            );
            $rapport->setUpdatedAt(new DateTimeImmutable());

            $this->manager->flush();

            return new JsonResponse(null, Response::HTTP_NO_CONTENT);
        }

        return new JsonResponse(null, Response::HTTP_NOT_FOUND);
    }

    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    /**
     * @OA\Delete(
     *     path="/api/rapport_veterinaire/{id}",
     *     summary="Supprimer un rapport vétérinaire par son ID",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID du rapport vétérinaire à supprimer",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Rapport supprimé avec succès"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Rapport non trouvé"
     *     )
     * )
     */
    public function delete(int $id): JsonResponse
    {
        $rapport = $this->repository->findOneBy(['id' => $id]);

        if ($rapport) {
            $this->manager->remove($rapport);
            $this->manager->flush();

            return new JsonResponse(null, Response::HTTP_NO_CONTENT);
        }

        return new JsonResponse(null, Response::HTTP_NOT_FOUND);
    }
}