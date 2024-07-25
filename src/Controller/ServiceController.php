<?php

namespace App\Controller;

use App\Entity\Signin;
use App\Repository\SigninRepository;
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

#[Route('/api/Signin', name:'app_api_Signin_')]
class SigninController extends AbstractController
{
    //$Signin->setUtilisateur($this-getUser()));
    
    public function __construct(
        private EntityManagerInterface $manager, 
        private SigninRepository $repository,
        private SerializerInterface $serializer,
        private UrlGeneratorInterface $urlGenerator,
        ) {
        
    }
    #[Route(methods:'POST')]


    /** @OA\Get(
     *     path="/api/Signin",
     *     summary="Créer un Signin ",
     *     @OA\RequestBody(
     *         required=true,
     *         description="Donner du Signin à créer",
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", example="Nom du Signin"),
     *             @OA\Property(property="description", type="string", example="Description du Signin")
     *         )
     * 
     *     ),
     *     @OA\Response(
     *         response=261,
     *         description="Signin crée avec succès",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="name", type="string", example="Nom du Signin"),
     *             @OA\Property(property="description", type="string", example="Description Signin"),
     *             @OA\Property(property="createdAt", type="string", format="date-time")
     *         )
     *     )
     *     )
     */

    public function new(Request $request): JsonResponse
{
    $Signin = $this->serializer->deserialize($request->getContent(), Signin::class, 'json');
    $Signin->setCreatedAt(new DateTimeImmutable());


    $this->manager->persist($Signin);
    $this->manager->flush();


    $responseData = $this->serializer->serialize($Signin, 'json');
    $location= $this->urlGenerator->generate(
        'app_api_Signin_show',
        ['id' => $Signin->getId()],
        referenceType: UrlGeneratorInterface::ABSOLUTE_URL,
    );

    return new JsonResponse($responseData, Response::HTTP_CREATED,["Location" => $location], true);

}

    #[Route('/{id}',name: 'show', methods:'GET')]

    /** @OA\Get(
     *     path="/api/Signin/{id}",
     *     summary="Afficher un Signin par son ID",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID du Signin à afficher",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Signin trouvé avec succès",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="name", type="string", example="Nom du Signin"),
     *             @OA\Property(property="description", type="string", example="Description du Signin"),
     *             @OA\Property(property="createdAt", type="string", format="date-time")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Signin non trouvé"
     *     )
     * )
     */

    public function show(int $id): JsonResponse
{
    $Signin = $this->repository->findOneBy(['id' => $id]);

    if ($Signin) {
        $responseData = $this->serializer->serialize($Signin, format: 'json');

        return new JsonResponse($responseData, Response::HTTP_OK, [], true);
    }

    return new JsonResponse(data: null, status: Response::HTTP_NOT_FOUND);

}

    #[Route('/{id}',name:'edit', methods:'PUT')]

/** 
 * @OA\Put(
 *     path="/api/Signin/{id}",
 *     summary="Mettre à jour un Signin par son ID",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="ID du Signin à mettre à jour",
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="name", type="string", example="Nom du Signin"),
 *             @OA\Property(property="description", type="string", example="Description du Signin")
 *         )
 *     ),
 *     @OA\Response(
 *         response=204,
 *         description="Signin mis à jour avec succès"
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Signin non trouvé"
 *     )
 * )
 */

    public function edit(int $id, Request $request): JsonResponse
{
    $Signin = $this->repository->findOneBy(['id' => $id]);
    if ($Signin){
        $Signin = $this->serializer->deserialize(
            $request->getContent(),
            Signin::class,
            'json',
            [AbstractNormalizer::OBJECT_TO_POPULATE => $Signin]
        );
        $Signin->setUpdateAt(new DateTimeImmutable());

        $this->manager->flush();

        return new JsonResponse(data: null, status: Response::HTTP_NO_CONTENT);
    }

    return new JsonResponse(data: null, status: Response::HTTP_NOT_FOUND);
    
}

    #[Route('/{id}',name:'delete', methods:'DELETE')]

/** 
 * @OA\Delete(
 *     path="/api/Signin/{id}",
 *     summary="Supprimer un Signin par son ID",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="ID du Signin à supprimer",
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=204,
 *         description="Signin supprimé avec succès"
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Signin non trouvé"
 *     )
 * )
 */

    public function delete(int $id): JsonResponse
{
    $Signin = $this->repository->findOneBy(['id' => $id]);
    if ($Signin) {
        $this->manager->remove($Signin);
        $this->manager->flush();

        return new JsonResponse(data: null, status: Response::HTTP_NO_CONTENT);
}
        

return new JsonResponse(data: null, status: Response::HTTP_NOT_FOUND);
}
}

