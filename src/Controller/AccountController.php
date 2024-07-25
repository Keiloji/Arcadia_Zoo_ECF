<?php

namespace App\Controller;

use App\Entity\Account;
use App\Repository\AccountRepository;
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

#[Route('/api/Account', name:'app_api_Account_')]
class AccountController extends AbstractController
{
    //$Account->setUtilisateur($this-getUser()));
    
    public function __construct(
        private EntityManagerInterface $manager, 
        private AccountRepository $repository,
        private SerializerInterface $serializer,
        private UrlGeneratorInterface $urlGenerator,
        ) {
        
    }
    #[Route(methods:'POST')]


    /** @OA\Get(
     *     path="/api/Account",
     *     summary="Créer un Account",
     *     @OA\RequestBody(
     *         required=true,
     *         description="Donner du Account à créer",
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", example="Nom du Account"),
     *             @OA\Property(property="description", type="string", example="Description du Account")
     *         )
     * 
     *     ),
     *     @OA\Response(
     *         response=261,
     *         description="Account crée avec succès",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="name", type="string", example="Nom du Account"),
     *             @OA\Property(property="description", type="string", example="Description Account"),
     *             @OA\Property(property="createdAt", type="string", format="date-time")
     *         )
     *     )
     *     )
     */

    public function new(Request $request): JsonResponse
{
    $Account = $this->serializer->deserialize($request->getContent(), Account::class, 'json');
    $Account->setCreatedAt(new DateTimeImmutable());


    $this->manager->persist($Account);
    $this->manager->flush();


    $responseData = $this->serializer->serialize($Account, 'json');
    $location= $this->urlGenerator->generate(
        'app_api_Account_show',
        ['id' => $Account->getId()],
        referenceType: UrlGeneratorInterface::ABSOLUTE_URL,
    );

    return new JsonResponse($responseData, Response::HTTP_CREATED,["Location" => $location], true);

}

    #[Route('/{id}',name: 'show', methods:'GET')]

/** 
 * @OA\Put(
 *     path="/api/Account/{id}",
 *     summary="Mettre à jour un Account par son ID",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="ID du Account à mettre à jour",
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="name", type="string", example="Nom du Account"),
 *             @OA\Property(property="description", type="string", example="Description du Account")
 *         )
 *     ),
 *     @OA\Response(
 *         response=204,
 *         description="Account mis à jour avec succès"
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Account non trouvé"
 *     )
 * )
 */

    public function show(int $id): JsonResponse
{
    $Account = $this->repository->findOneBy(['id' => $id]);

    if ($Account) {
        $responseData = $this->serializer->serialize($Account, format: 'json');

        return new JsonResponse($responseData, Response::HTTP_OK, [], true);
    }

    return new JsonResponse(data: null, status: Response::HTTP_NOT_FOUND);

}

    #[Route('/{id}',name:'edit', methods:'PUT')]

/** 
 * @OA\Put(
 *     path="/api/Account/{id}",
 *     summary="Mettre à jour un Account par son ID",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="ID du Account à mettre à jour",
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="name", type="string", example="Nom du Account"),
 *             @OA\Property(property="description", type="string", example="Description du Account")
 *         )
 *     ),
 *     @OA\Response(
 *         response=204,
 *         description="Account mis à jour avec succès"
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Account non trouvé"
 *     )
 * )
 */

    public function edit(int $id, Request $request): JsonResponse
{
    $Account = $this->repository->findOneBy(['id' => $id]);
    if ($Account){
        $Account= $this->serializer->deserialize(
            $request->getContent(),
            Account::class,
            'json',
            [AbstractNormalizer::OBJECT_TO_POPULATE => $Account]
        );
        $Account->setUpdateAt(new DateTimeImmutable());

        $this->manager->flush();

        return new JsonResponse(data: null, status: Response::HTTP_NO_CONTENT);
    }

    return new JsonResponse(data: null, status: Response::HTTP_NOT_FOUND);
    
}

    #[Route('/{id}',name:'delete', methods:'DELETE')]

/** 
 * @OA\Delete(
 *     path="/api/Account/{id}",
 *     summary="Supprimer un Account par son ID",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="ID de Account à supprimer",
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=204,
 *         description="Account supprimé avec succès"
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Account non trouvé"
 *     )
 * )
 */

    public function delete(int $id): JsonResponse
{
    $Account= $this->repository->findOneBy(['id' => $id]);
    if ($Account) {
        $this->manager->remove($Account);
        $this->manager->flush();

        return new JsonResponse(data: null, status: Response::HTTP_NO_CONTENT);
}
        

return new JsonResponse(data: null, status: Response::HTTP_NOT_FOUND);
}
}

