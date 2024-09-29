<?php

namespace App\Controller;

use App\Entity\Race;
use App\Repository\RaceRepository;
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

#[Route('/api/Race', name:'app_api_Race_')]
class RaceController extends AbstractController
{
    //$Race->setUtilisateur($this-getUser()));
    
    public function __construct(
        private EntityManagerInterface $manager, 
        private RaceRepository $repository,
        private SerializerInterface $serializer,
        private UrlGeneratorInterface $urlGenerator,
        ) {
        
    }
    #[Route(methods:'POST')]


    /** @OA\Get(
     *     path="/api/Race",
     *     summary="Créer un Race ",
     *     @OA\RequestBody(
     *         required=true,
     *         description="Donner du Race à créer",
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", example="Nom du Race"),
     *             @OA\Property(property="description", type="string", example="Description du Race")
     *         )
     * 
     *     ),
     *     @OA\Response(
     *         response=261,
     *         description="Race crée avec succès",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="name", type="string", example="Nom du Race"),
     *             @OA\Property(property="description", type="string", example="Description Race"),
     *             @OA\Property(property="createdAt", type="string", format="date-time")
     *         )
     *     )
     *     )
     */

    public function new(Request $request): JsonResponse
{
    $Race = $this->serializer->deserialize($request->getContent(), Race::class, 'json');
    $Race->setCreatedAt(new DateTimeImmutable());


    $this->manager->persist($Race);
    $this->manager->flush();


    $responseData = $this->serializer->serialize($Race, 'json');
    $location= $this->urlGenerator->generate(
        'app_api_Race_show',
        ['id' => $Race->getId()],
        referenceType: UrlGeneratorInterface::ABSOLUTE_URL,
    );

    return new JsonResponse($responseData, Response::HTTP_CREATED,["Location" => $location], true);

}

    #[Route('/{id}',name: 'show', methods:'GET')]

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
 *     @OA\Response(
 *         response=204,
 *         description="Race mis à jour avec succès"
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Race non trouvé"
 *     )
 * )
 */

    public function show(int $id): JsonResponse
{
    $Race = $this->repository->findOneBy(['id' => $id]);

    if ($Race) {
        $responseData = $this->serializer->serialize($Race, format: 'json');

        return new JsonResponse($responseData, Response::HTTP_OK, [], true);
    }

    return new JsonResponse(data: null, status: Response::HTTP_NOT_FOUND);

}

    #[Route('/{id}',name:'edit', methods:'PUT')]

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
 *     @OA\Response(
 *         response=204,
 *         description="Race mis à jour avec succès"
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Race non trouvé"
 *     )
 * )
 */

    public function edit(int $id, Request $request): JsonResponse
{
    $Race = $this->repository->findOneBy(['id' => $id]);
    if ($Race){
        $Race = $this->serializer->deserialize(
            $request->getContent(),
            Race::class,
            'json',
            [AbstractNormalizer::OBJECT_TO_POPULATE => $Race]
        );
        $Race->setUpdateAt(new DateTimeImmutable());

        $this->manager->flush();

        return new JsonResponse(data: null, status: Response::HTTP_NO_CONTENT);
    }

    return new JsonResponse(data: null, status: Response::HTTP_NOT_FOUND);
    
}

    #[Route('/{id}',name:'delete', methods:'DELETE')]

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
 *     @OA\Response(
 *         response=204,
 *         description="Race supprimé avec succès"
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Race non trouvé"
 *     )
 * )
 */

    public function delete(int $id): JsonResponse
{
    $Race = $this->repository->findOneBy(['id' => $id]);
    if ($Race) {
        $this->manager->remove($Race);
        $this->manager->flush();

        return new JsonResponse(data: null, status: Response::HTTP_NO_CONTENT);
}
        

return new JsonResponse(data: null, status: Response::HTTP_NOT_FOUND);
}
}

