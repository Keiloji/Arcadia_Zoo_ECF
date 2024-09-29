<?php

namespace App\Controller;

use App\Entity\Home;
use App\Repository\HomeRepository;
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

#[Route('/api/Home', name: 'app_api_Home_')]
class HomeController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $manager,
        private HomeRepository $repository,
        private SerializerInterface $serializer,
        private UrlGeneratorInterface $urlGenerator
    ) {
    }

    #[Route(methods: 'POST')]
    /**
     * @OA\Post(
     *     path="/api/Home",
     *     summary="Créer un Home",
     *     @OA\RequestBody(
     *         required=true,
     *         description="Données du Home à créer",
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", example="Nom du Home"),
     *             @OA\Property(property="description", type="string", example="Description du Home")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Home créé avec succès",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="name", type="string", example="Nom du Home"),
     *             @OA\Property(property="description", type="string", example="Description du Home"),
     *             @OA\Property(property="createdAt", type="string", format="date-time")
     *         )
     *     )
     * )
     */
    public function new(Request $request): JsonResponse
    {
        $home = $this->serializer->deserialize($request->getContent(), Home::class, 'json');
        $home->setCreatedAt(new DateTimeImmutable());

        $this->manager->persist($home);
        $this->manager->flush();

        $responseData = $this->serializer->serialize($home, 'json');
        $location = $this->urlGenerator->generate(
            'app_api_Home_show',
            ['id' => $home->getId()],
            UrlGeneratorInterface::ABSOLUTE_URL
        );

        return new JsonResponse($responseData, Response::HTTP_CREATED, ['Location' => $location], true);
    }

    #[Route('/{id}', name: 'show', methods: 'GET')]
    /**
     * @OA\Get(
     *     path="/api/Home/{id}",
     *     summary="Obtenir un Home par son ID",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID du Home",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Home trouvé",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="name", type="string", example="Nom du Home"),
     *             @OA\Property(property="description", type="string", example="Description du Home"),
     *             @OA\Property(property="createdAt", type="string", format="date-time")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Home non trouvé"
     *     )
     * )
     */
    public function show(int $id): JsonResponse
    {
        $home = $this->repository->find($id);

        if ($home) {
            $responseData = $this->serializer->serialize($home, 'json');
            return new JsonResponse($responseData, Response::HTTP_OK, [], true);
        }

        return new JsonResponse(null, Response::HTTP_NOT_FOUND);
    }

    #[Route('/{id}', name: 'edit', methods: 'PUT')]
    /**
     * @OA\Put(
     *     path="/api/Home/{id}",
     *     summary="Mettre à jour un Home par son ID",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID du Home à mettre à jour",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="name", type="string", example="Nom du Home"),
     *             @OA\Property(property="description", type="string", example="Description du Home")
     *         )
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Home mis à jour avec succès"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Home non trouvé"
     *     )
     * )
     */
    public function edit(int $id, Request $request): JsonResponse
    {
        $home = $this->repository->find($id);

        if ($home) {
            $this->serializer->deserialize(
                $request->getContent(),
                Home::class,
                'json',
                [AbstractNormalizer::OBJECT_TO_POPULATE => $home]
            );
            $home->setUpdatedAt(new DateTimeImmutable());

            $this->manager->flush();

            return new JsonResponse(null, Response::HTTP_NO_CONTENT);
        }

        return new JsonResponse(null, Response::HTTP_NOT_FOUND);
    }

    #[Route('/{id}', name: 'delete', methods: 'DELETE')]
    /**
     * @OA\Delete(
     *     path="/api/Home/{id}",
     *     summary="Supprimer un Home par son ID",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID du Home à supprimer",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Home supprimé avec succès"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Home non trouvé"
     *     )
     * )
     */
    public function delete(int $id): JsonResponse
    {
        $home = $this->repository->find($id);

        if ($home) {
            $this->manager->remove($home);
            $this->manager->flush();

            return new JsonResponse(null, Response::HTTP_NO_CONTENT);
        }

        return new JsonResponse(null, Response::HTTP_NOT_FOUND);
    }
}