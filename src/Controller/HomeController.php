<?php

namespace App\Controller;

use App\Entity\Home;
use App\Repository\HomeRepository;
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

#[Route('/api/Home', name:'app_api_Home_')]
class HomeController extends AbstractController
{
    //$Home->setUtilisateur($this-getUser()));
    
    public function __construct(
        private EntityManagerInterface $manager, 
        private HomeRepository $repository,
        private SerializerInterface $serializer,
        private UrlGeneratorInterface $urlGenerator,
        ) {
        
    }
    #[Route(methods:'POST')]


    /** @OA\Get(
     *     path="/api/Home",
     *     summary="Créer un Home ",
     *     @OA\RequestBody(
     *         required=true,
     *         description="Donner du Home à créer",
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", example="Nom du Home"),
     *             @OA\Property(property="description", type="string", example="Description du Home")
     *         )
     * 
     *     ),
     *     @OA\Response(
     *         response=261,
     *         description="Home crée avec succès",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="name", type="string", example="Nom du Home"),
     *             @OA\Property(property="description", type="string", example="Description Home"),
     *             @OA\Property(property="createdAt", type="string", format="date-time")
     *         )
     *     )
     *     )
     */

    public function new(Request $request): JsonResponse
{
    $Home = $this->serializer->deserialize($request->getContent(), Home::class, 'json');
    $Home->setCreatedAt(new DateTimeImmutable());


    $this->manager->persist($Home);
    $this->manager->flush();


    $responseData = $this->serializer->serialize($Home, 'json');
    $location= $this->urlGenerator->generate(
        'app_api_Home_show',
        ['id' => $Home->getId()],
        referenceType: UrlGeneratorInterface::ABSOLUTE_URL,
    );

    return new JsonResponse($responseData, Response::HTTP_CREATED,["Location" => $location], true);

}

    #[Route('/{id}',name: 'show', methods:'GET')]

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

    public function show(int $id): JsonResponse
{
    $Home = $this->repository->findOneBy(['id' => $id]);

    if ($Home) {
        $responseData = $this->serializer->serialize($Home, format: 'json');

        return new JsonResponse($responseData, Response::HTTP_OK, [], true);
    }

    return new JsonResponse(data: null, status: Response::HTTP_NOT_FOUND);

}

    #[Route('/{id}',name:'edit', methods:'PUT')]

/** 
 * @OA\Put(
 *     path="/api/Image/{id}",
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
    $Home = $this->repository->findOneBy(['id' => $id]);
    if ($Home){
        $Home= $this->serializer->deserialize(
            $request->getContent(),
            Home::class,
            'json',
            [AbstractNormalizer::OBJECT_TO_POPULATE => $Home]
        );
        $Home->setUpdateAt(new DateTimeImmutable());

        $this->manager->flush();

        return new JsonResponse(data: null, status: Response::HTTP_NO_CONTENT);
    }

    return new JsonResponse(data: null, status: Response::HTTP_NOT_FOUND);
    
}

    #[Route('/{id}',name:'delete', methods:'DELETE')]

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
    $Home = $this->repository->findOneBy(['id' => $id]);
    if ($Home) {
        $this->manager->remove($Home);
        $this->manager->flush();

        return new JsonResponse(data: null, status: Response::HTTP_NO_CONTENT);
}
        

return new JsonResponse(data: null, status: Response::HTTP_NOT_FOUND);
}
}

