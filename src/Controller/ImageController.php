<?php

namespace App\Controller;

use App\Entity\Image;
use App\Repository\ImageRepository;
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

#[Route('/api/Image', name: 'app_api_Image_')]
class ImageController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $manager,
        private ImageRepository $repository,
        private SerializerInterface $serializer,
        private UrlGeneratorInterface $urlGenerator
    ) {
    }

    #[Route(methods: 'POST')]
    /**
     * @OA\Post(
     *     path="/api/Image",
     *     summary="Créer une image",
     *     @OA\RequestBody(
     *         required=true,
     *         description="Données de l'image à créer",
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", example="Nom de l'image"),
     *             @OA\Property(property="description", type="string", example="Description de l'image")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Image créée avec succès",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="name", type="string", example="Nom de l'image"),
     *             @OA\Property(property="description", type="string", example="Description de l'image"),
     *             @OA\Property(property="createdAt", type="string", format="date-time")
     *         )
     *     )
     * )
     */
    public function new(Request $request): JsonResponse
    {
        $image = $this->serializer->deserialize($request->getContent(), Image::class, 'json');
        $image->setCreatedAt(new DateTimeImmutable());

        $this->manager->persist($image);
        $this->manager->flush();

        $responseData = $this->serializer->serialize($image, 'json');
        $location = $this->urlGenerator->generate(
            'app_api_Image_show',
            ['id' => $image->getId()],
            UrlGeneratorInterface::ABSOLUTE_URL
        );

        return new JsonResponse($responseData, Response::HTTP_CREATED, ['Location' => $location], true);
    }

    #[Route('/{id}', name: 'show', methods: 'GET')]
    /**
     * @OA\Get(
     *     path="/api/Image/{id}",
     *     summary="Obtenir une image par son ID",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de l'image",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Image trouvée",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="name", type="string", example="Nom de l'image"),
     *             @OA\Property(property="description", type="string", example="Description de l'image"),
     *             @OA\Property(property="createdAt", type="string", format="date-time")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Image non trouvée"
     *     )
     * )
     */
    public function show(int $id): JsonResponse
    {
        $image = $this->repository->find($id);

        if ($image) {
            $responseData = $this->serializer->serialize($image, 'json');
            return new JsonResponse($responseData, Response::HTTP_OK, [], true);
        }

        return new JsonResponse(null, Response::HTTP_NOT_FOUND);
    }

    #[Route('/{id}', name: 'edit', methods: 'PUT')]
    /**
     * @OA\Put(
     *     path="/api/Image/{id}",
     *     summary="Mettre à jour une image par son ID",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de l'image à mettre à jour",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="name", type="string", example="Nom de l'image"),
     *             @OA\Property(property="description", type="string", example="Description de l'image")
     *         )
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Image mise à jour avec succès"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Image non trouvée"
     *     )
     * )
     */
    public function edit(int $id, Request $request): JsonResponse
    {
        $image = $this->repository->find($id);

        if ($image) {
            $this->serializer->deserialize(
                $request->getContent(),
                Image::class,
                'json',
                [AbstractNormalizer::OBJECT_TO_POPULATE => $image]
            );
            $image->setUpdatedAt(new DateTimeImmutable());

            $this->manager->flush();

            return new JsonResponse(null, Response::HTTP_NO_CONTENT);
        }

        return new JsonResponse(null, Response::HTTP_NOT_FOUND);
    }

    #[Route('/{id}', name: 'delete', methods: 'DELETE')]
    /**
     * @OA\Delete(
     *     path="/api/Image/{id}",
     *     summary="Supprimer une image par son ID",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de l'image à supprimer",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Image supprimée avec succès"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Image non trouvée"
     *     )
     * )
     */
    public function delete(int $id): JsonResponse
    {
        $image = $this->repository->find($id);

        if ($image) {
            $this->manager->remove($image);
            $this->manager->flush();

            return new JsonResponse(null, Response::HTTP_NO_CONTENT);
        }

        return new JsonResponse(null, Response::HTTP_NOT_FOUND);
    }
}