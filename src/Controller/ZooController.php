<?php

namespace App\Controller;

use App\Entity\Zoo;
use App\Repository\ZooRepository;
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

#[Route('/api/zoo', name:'app_api_zoo_')]
class ZooController extends AbstractController
{
    //$zoo->setUtilisateur($this-getUser()));
    
    public function __construct(
        private EntityManagerInterface $manager, 
        private ZooRepository $repository,
        private SerializerInterface $serializer,
        private UrlGeneratorInterface $urlGenerator,
        ) {
        
    }
    #[Route(methods:'POST')]


    /** @OA\Get(
     *     path="/api/zoo",
     *     summary="Créer un zoo ",
     *     @OA\RequestBody(
     *         required=true,
     *         description="Donner du zoo à créer",
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", example="Nom du Zoo"),
     *             @OA\Property(property="description", type="string", example="Description du Zoo")
     *         )
     * 
     *     ),
     *     @OA\Response(
     *         response=261,
     *         description="Zoo crée avec succès",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="name", type="string", example="Nom du Zoo"),
     *             @OA\Property(property="description", type="string", example="Description du Zoo"),
     *             @OA\Property(property="createdAt", type="string", format="date-time")
     *         )
     *     )
     *     )
     */

    public function new(Request $request): JsonResponse
{
    $zoo = $this->serializer->deserialize($request->getContent(), Zoo::class, 'json');
    $zoo->setCreatedAt(new DateTimeImmutable());


    $this->manager->persist($zoo);
    $this->manager->flush();


    $responseData = $this->serializer->serialize($zoo, 'json');
    $location= $this->urlGenerator->generate(
        'app_api_zoo_show',
        ['id' => $zoo->getId()],
        referenceType: UrlGeneratorInterface::ABSOLUTE_URL,
    );

    return new JsonResponse($responseData, Response::HTTP_CREATED,["Location" => $location], true);

}

    #[Route('/{id}',name: 'show', methods:'GET')]

    /** @OA\Get(
     *     path="/api/zoo/{id}",
     *     summary="Afficher un zoo par son ID",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID du zoo à afficher",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Zoo trouvé avec succès",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="name", type="string", example="Nom du Zoo"),
     *             @OA\Property(property="description", type="string", example="Description du Zoo"),
     *             @OA\Property(property="createdAt", type="string", format="date-time")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Zoo non trouvé"
     *     )
     * )
     */

    public function show(int $id): JsonResponse
{
    $zoo = $this->repository->findOneBy(['id' => $id]);

    if ($zoo) {
        $responseData = $this->serializer->serialize($zoo, format: 'json');

        return new JsonResponse($responseData, Response::HTTP_OK, [], true);
    }

    return new JsonResponse(data: null, status: Response::HTTP_NOT_FOUND);

}

    #[Route('/{id}',name:'edit', methods:'PUT')]

/** 
 * @OA\Put(
 *     path="/api/zoo/{id}",
 *     summary="Mettre à jour un zoo par son ID",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="ID du zoo à mettre à jour",
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="name", type="string", example="Nom du Zoo"),
 *             @OA\Property(property="description", type="string", example="Description du Zoo")
 *         )
 *     ),
 *     @OA\Response(
 *         response=204,
 *         description="Zoo mis à jour avec succès"
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Zoo non trouvé"
 *     )
 * )
 */

    public function edit(int $id, Request $request): JsonResponse
{
    $zoo = $this->repository->findOneBy(['id' => $id]);
    if ($zoo){
        $zoo = $this->serializer->deserialize(
            $request->getContent(),
            Zoo::class,
            'json',
            [AbstractNormalizer::OBJECT_TO_POPULATE => $zoo]
        );
        $zoo->setUpdateAt(new DateTimeImmutable());

        $this->manager->flush();

        return new JsonResponse(data: null, status: Response::HTTP_NO_CONTENT);
    }

    return new JsonResponse(data: null, status: Response::HTTP_NOT_FOUND);
    
}

    #[Route('/{id}',name:'delete', methods:'DELETE')]

/** 
 * @OA\Delete(
 *     path="/api/zoo/{id}",
 *     summary="Supprimer un zoo par son ID",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="ID du zoo à supprimer",
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=204,
 *         description="Zoo supprimé avec succès"
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Zoo non trouvé"
 *     )
 * )
 */

    public function delete(int $id): JsonResponse
{
    $zoo = $this->repository->findOneBy(['id' => $id]);
    if ($zoo) {
        $this->manager->remove($zoo);
        $this->manager->flush();

        return new JsonResponse(data: null, status: Response::HTTP_NO_CONTENT);
}
        

return new JsonResponse(data: null, status: Response::HTTP_NOT_FOUND);
}
}

