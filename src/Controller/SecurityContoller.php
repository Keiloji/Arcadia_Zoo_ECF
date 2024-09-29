<?php

namespace App\Controller;

use App\Entity\Security; 
use App\Repository\SecurityRepository; 
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

#[Route('/api/Security', name:'app_api_Security_')]
class SecurityController extends AbstractController
{
    private EntityManagerInterface $manager;
    private SecurityRepository $repository;
    private SerializerInterface $serializer;
    private UrlGeneratorInterface $urlGenerator;

    public function __construct(
        EntityManagerInterface $manager, 
        SecurityRepository $repository,
        SerializerInterface $serializer,
        UrlGeneratorInterface $urlGenerator
    ) {
        $this->manager = $manager;
        $this->repository = $repository;
        $this->serializer = $serializer;
        $this->urlGenerator = $urlGenerator;
    }

    #[Route(methods:'POST')]
    /**
     * @OA\Post(
     *     path="/api/Security",
     *     summary="Créer un nouveau Security",
     *     @OA\RequestBody(
     *         required=true,
     *         description="Données pour créer un Security",
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", example="Nom du Security"),
     *             @OA\Property(property="description", type="string", example="Description du Security")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Security créé avec succès",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="name", type="string", example="Nom du Security"),
     *             @OA\Property(property="description", type="string", example="Description du Security"),
     *             @OA\Property(property="createdAt", type="string", format="date-time")
     *         )
     *     )
     * )
     */
    public function new(Request $request): JsonResponse
    {
        $security = $this->serializer->deserialize($request->getContent(), Security::class, 'json');
        $security->setCreatedAt(new DateTimeImmutable());

        $this->manager->persist($security);
        $this->manager->flush();

        $responseData = $this->serializer->serialize($security, 'json');
        $location = $this->urlGenerator->generate(
            'app_api_Security_show',
            ['id' => $security->getId()],
            UrlGeneratorInterface::ABSOLUTE_URL
        );

        return new JsonResponse($responseData, Response::HTTP_CREATED, ["Location" => $location], true);
    }

    #[Route('/{id}', name: 'show', methods:'GET')]
    /**
     * @OA\Get(
     *     path="/api/Security/{id}",
     *     summary="Afficher un Security par son ID",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID du Security à afficher",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Security trouvé avec succès",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="name", type="string", example="Nom du Security"),
     *             @OA\Property(property="description", type="string", example="Description du Security"),
     *             @OA\Property(property="createdAt", type="string", format="date-time")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Security non trouvé"
     *     )
     * )
     */
    public function show(int $id): JsonResponse
    {
        $security = $this->repository->findOneBy(['id' => $id]);

        if ($security) {
            $responseData = $this->serializer->serialize($security, 'json');
            return new JsonResponse($responseData, Response::HTTP_OK, [], true);
        }

        return new JsonResponse(null, Response::HTTP_NOT_FOUND);
    }

    #[Route('/{id}', name:'edit', methods:'PUT')]
    /**
     * @OA\Put(
     *     path="/api/Security/{id}",
     *     summary="Mettre à jour un Security par son ID",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID du Security à mettre à jour",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="name", type="string", example="Nom du Security"),
     *             @OA\Property(property="description", type="string", example="Description du Security")
     *         )
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Security mis à jour avec succès"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Security non trouvé"
     *     )
     * )
     */
    public function edit(int $id, Request $request): JsonResponse
    {
        $security = $this->repository->findOneBy(['id' => $id]);

        if ($security) {
            $security = $this->serializer->deserialize(
                $request->getContent(),
                Security::class,
                'json',
                [AbstractNormalizer::OBJECT_TO_POPULATE => $security]
            );
            $security->setUpdatedAt(new DateTimeImmutable());

            $this->manager->flush();

            return new JsonResponse(null, Response::HTTP_NO_CONTENT);
        }

        return new JsonResponse(null, Response::HTTP_NOT_FOUND);
    }

    #[Route('/{id}', name:'delete', methods:'DELETE')]
    /**
     * @OA\Delete(
     *     path="/api/Security/{id}",
     *     summary="Supprimer un Security par son ID",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID du Security à supprimer",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Security supprimé avec succès"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Security non trouvé"
     *     )
     * )
     */
    public function delete(int $id): JsonResponse
    {
        $security = $this->repository->findOneBy(['id' => $id]);

        if ($security) {
            $this->manager->remove($security);
            $this->manager->flush();

            return new JsonResponse(null, Response::HTTP_NO_CONTENT);
        }

        return new JsonResponse(null, Response::HTTP_NOT_FOUND);
    }
}