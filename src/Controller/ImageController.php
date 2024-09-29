<?php

namespace App\Controller;

use App\Entity\Image;
use App\Repository\ImageRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use OpenApi\Annotations as OA;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Flex\Response as FlexResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/api/Image', name:'app_api_Image_')]
class ImageController extends AbstractController
{
    //$Image->setUtilisateur($this-getUser()));
    
    public function __construct(
        private EntityManagerInterface $manager, 
        private ImageRepository $repository,
        private SerializerInterface $serializer,
        private UrlGeneratorInterface $urlGenerator,
        ) {
        
    }
    #[Route(methods:'POST')]


    /** @OA\Get(
     *     path="/api/Race",
     *     summary="Créer un Image ",
     *     @OA\RequestBody(
     *         required=true,
     *         description="Donner du Image à créer",
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", example="Nom du Image"),
     *             @OA\Property(property="description", type="string", example="Description du Image")
     *         )
     * 
     *     ),
     *     @OA\Response(
     *         response=261,
     *         description="Image crée avec succès",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="name", type="string", example="Nom du Image"),
     *             @OA\Property(property="description", type="string", example="Description Image"),
     *             @OA\Property(property="createdAt", type="string", format="date-time")
     *         )
     *     )
     *     )
     */

    public function new(Request $request): JsonResponse
{
    $Image = $this->serializer->deserialize($request->getContent(), Image::class, 'json');
    $Image->setCreatedAt(new DateTimeImmutable());


    $this->manager->persist($Image);
    $this->manager->flush();


    $responseData = $this->serializer->serialize($Image, 'json');
    $location= $this->urlGenerator->generate(
        'app_api_Image_show',
        ['id' => $Image->getId()],
        referenceType: UrlGeneratorInterface::ABSOLUTE_URL,
    );

    return new JsonResponse($responseData, Response::HTTP_CREATED,["Location" => $location], true);

}

    #[Route('/{id}',name: 'show', methods:'GET')]

/** 
 * @OA\Put(
 *     path="/api/Image/{id}",
 *     summary="Mettre à jour un Image par son ID",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="ID du Image à mettre à jour",
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="name", type="string", example="Nom du Image"),
 *             @OA\Property(property="description", type="string", example="Description du Image")
 *         )
 *     ),
 *     @OA\Response(
 *         response=204,
 *         description="Image mis à jour avec succès"
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Image non trouvé"
 *     )
 * )
 */

    public function show(int $id): JsonResponse
{
    $Image = $this->repository->findOneBy(['id' => $id]);

    if ($Image) {
        $responseData = $this->serializer->serialize($Image, format: 'json');

        return new JsonResponse($responseData, Response::HTTP_OK, [], true);
    }

    return new JsonResponse(data: null, status: Response::HTTP_NOT_FOUND);

}

    #[Route('/{id}',name:'edit', methods:'PUT')]

/** 
 * @OA\Put(
 *     path="/api/Image/{id}",
 *     summary="Mettre à jour un Image par son ID",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="ID du Image à mettre à jour",
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="name", type="string", example="Nom du Image"),
 *             @OA\Property(property="description", type="string", example="Description du Image")
 *         )
 *     ),
 *     @OA\Response(
 *         response=204,
 *         description="Image mis à jour avec succès"
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Image non trouvé"
 *     )
 * )
 */

    public function edit(int $id, Request $request): JsonResponse
{
    $Image = $this->repository->findOneBy(['id' => $id]);
    if ($Image){
        $Image = $this->serializer->deserialize(
            $request->getContent(),
            Image::class,
            'json',
            [AbstractNormalizer::OBJECT_TO_POPULATE => $Image]
        );
        $Image->setUpdateAt(new DateTimeImmutable());

        $this->manager->flush();

        return new JsonResponse(data: null, status: Response::HTTP_NO_CONTENT);
    }

    return new JsonResponse(data: null, status: Response::HTTP_NOT_FOUND);
    
}

    #[Route('/{id}',name:'delete', methods:'DELETE')]

/** 
 * @OA\Delete(
 *     path="/api/Image/{id}",
 *     summary="Supprimer un Image par son ID",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="ID du Image à supprimer",
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=204,
 *         description="Image supprimé avec succès"
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Image non trouvé"
 *     )
 * )
 */

    public function delete(int $id): JsonResponse
{
    $Image = $this->repository->findOneBy(['id' => $id]);
    if ($Image) {
        $this->manager->remove($Image);
        $this->manager->flush();

        return new JsonResponse(data: null, status: Response::HTTP_NO_CONTENT);
}
        

return new JsonResponse(data: null, status: Response::HTTP_NOT_FOUND);
}
}

