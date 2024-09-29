<?php

namespace App\Controller;

use App\Entity\Signup;
use App\Repository\SignupRepository;
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

#[Route('/api/Signup', name:'app_api_Signup_')]
class SignupController extends AbstractController
{
    //$Signup->setUtilisateur($this-getUser()));
    
    public function __construct(
        private EntityManagerInterface $manager, 
        private SignupRepository $repository,
        private SerializerInterface $serializer,
        private UrlGeneratorInterface $urlGenerator,
        ) {
        
    }
    #[Route(methods:'POST')]


    /** @OA\Get(
     *     path="/api/Signup",
     *     summary="Créer un Signup ",
     *     @OA\RequestBody(
     *         required=true,
     *         description="Donner du Signup à créer",
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", example="Nom du Signup"),
     *             @OA\Property(property="description", type="string", example="Description du Signup")
     *         )
     * 
     *     ),
     *     @OA\Response(
     *         response=261,
     *         description="Signup crée avec succès",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="name", type="string", example="Nom du Signup"),
     *             @OA\Property(property="description", type="string", example="Description Signup"),
     *             @OA\Property(property="createdAt", type="string", format="date-time")
     *         )
     *     )
     *     )
     */

    public function new(Request $request): JsonResponse
{
    $Signup = $this->serializer->deserialize($request->getContent(), Signup::class, 'json');
    $Signup->setCreatedAt(new DateTimeImmutable());


    $this->manager->persist($Signup);
    $this->manager->flush();


    $responseData = $this->serializer->serialize($Signup, 'json');
    $location= $this->urlGenerator->generate(
        'app_api_Signup_show',
        ['id' => $Signup->getId()],
        referenceType: UrlGeneratorInterface::ABSOLUTE_URL,
    );

    return new JsonResponse($responseData, Response::HTTP_CREATED,["Location" => $location], true);

}

    #[Route('/{id}',name: 'show', methods:'GET')]

    /** @OA\Get(
     *     path="/api/Signup/{id}",
     *     summary="Afficher un Signup par son ID",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID du Signup à afficher",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Signup trouvé avec succès",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="name", type="string", example="Nom du Signup"),
     *             @OA\Property(property="description", type="string", example="Description du Signup"),
     *             @OA\Property(property="createdAt", type="string", format="date-time")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Signup non trouvé"
     *     )
     * )
     */

    public function show(int $id): JsonResponse
{
    $Signup = $this->repository->findOneBy(['id' => $id]);

    if ($Signup) {
        $responseData = $this->serializer->serialize($Signup, format: 'json');

        return new JsonResponse($responseData, Response::HTTP_OK, [], true);
    }

    return new JsonResponse(data: null, status: Response::HTTP_NOT_FOUND);

}

    #[Route('/{id}',name:'edit', methods:'PUT')]

/** 
 * @OA\Put(
 *     path="/api/Signup/{id}",
 *     summary="Mettre à jour un Signup par son ID",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="ID du Signup à mettre à jour",
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="name", type="string", example="Nom du Zoo"),
 *             @OA\Property(property="description", type="string", example="Description du Signup")
 *         )
 *     ),
 *     @OA\Response(
 *         response=204,
 *         description="Signup mis à jour avec succès"
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Signup non trouvé"
 *     )
 * )
 */

    public function edit(int $id, Request $request): JsonResponse
{
    $Signup = $this->repository->findOneBy(['id' => $id]);
    if ($Signup){
        $Signup = $this->serializer->deserialize(
            $request->getContent(),
            Signup::class,
            'json',
            [AbstractNormalizer::OBJECT_TO_POPULATE => $Signup]
        );
        $Signup->setUpdateAt(new DateTimeImmutable());

        $this->manager->flush();

        return new JsonResponse(data: null, status: Response::HTTP_NO_CONTENT);
    }

    return new JsonResponse(data: null, status: Response::HTTP_NOT_FOUND);
    
}

    #[Route('/{id}',name:'delete', methods:'DELETE')]

/** 
 * @OA\Delete(
 *     path="/api/Signup/{id}",
 *     summary="Supprimer un Signup par son ID",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="ID du Signup à supprimer",
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=204,
 *         description="Signup supprimé avec succès"
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Signup non trouvé"
 *     )
 * )
 */

    public function delete(int $id): JsonResponse
{
    $Signup = $this->repository->findOneBy(['id' => $id]);
    if ($Signup) {
        $this->manager->remove($Signup);
        $this->manager->flush();

        return new JsonResponse(data: null, status: Response::HTTP_NO_CONTENT);
}
        

return new JsonResponse(data: null, status: Response::HTTP_NOT_FOUND);
}
}

