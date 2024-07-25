<?php

namespace App\Controller;

use App\Entity\Role;
use App\Repository\RoleRepository;
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

#[Route('/api/Role', name:'app_api_Role_')]
class RoleController extends AbstractController
{
    //$Role->setUtilisateur($this-getUser()));
    
    public function __construct(
        private EntityManagerInterface $manager, 
        private RoleRepository $repository,
        private SerializerInterface $serializer,
        private UrlGeneratorInterface $urlGenerator,
        ) {
        
    }
    #[Route(methods:'POST')]


    /** @OA\Get(
     *     path="/api/Role",
     *     summary="Créer un Role ",
     *     @OA\RequestBody(
     *         required=true,
     *         description="Donner du Role à créer",
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", example="Nom du Role"),
     *             @OA\Property(property="description", type="string", example="Description du Role")
     *         )
     * 
     *     ),
     *     @OA\Response(
     *         response=261,
     *         description="Role crée avec succès",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="name", type="string", example="Nom du Role"),
     *             @OA\Property(property="description", type="string", example="Description Role"),
     *             @OA\Property(property="createdAt", type="string", format="date-time")
     *         )
     *     )
     *     )
     */

    public function new(Request $request): JsonResponse
{
    $Role = $this->serializer->deserialize($request->getContent(), Role::class, 'json');
    $Role->setCreatedAt(new DateTimeImmutable());


    $this->manager->persist($Role);
    $this->manager->flush();


    $responseData = $this->serializer->serialize($Role, 'json');
    $location= $this->urlGenerator->generate(
        'app_api_Role_show',
        ['id' => $Role->getId()],
        referenceType: UrlGeneratorInterface::ABSOLUTE_URL,
    );

    return new JsonResponse($responseData, Response::HTTP_CREATED,["Location" => $location], true);

}

    #[Route('/{id}',name: 'show', methods:'GET')]

    /** @OA\Get(
     *     path="/api/Role/{id}",
     *     summary="Afficher un Role par son ID",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID du Role à afficher",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Role trouvé avec succès",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="name", type="string", example="Nom du Role"),
     *             @OA\Property(property="description", type="string", example="Description du Role"),
     *             @OA\Property(property="createdAt", type="string", format="date-time")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Role non trouvé"
     *     )
     * )
     */

    public function show(int $id): JsonResponse
{
    $Role = $this->repository->findOneBy(['id' => $id]);

    if ($Role) {
        $responseData = $this->serializer->serialize($Role, format: 'json');

        return new JsonResponse($responseData, Response::HTTP_OK, [], true);
    }

    return new JsonResponse(data: null, status: Response::HTTP_NOT_FOUND);

}

    #[Route('/{id}',name:'edit', methods:'PUT')]

/** 
 * @OA\Put(
 *     path="/api/Role/{id}",
 *     summary="Mettre à jour un Role par son ID",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="ID du Role à mettre à jour",
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="name", type="string", example="Nom du Role"),
 *             @OA\Property(property="description", type="string", example="Description du Role")
 *         )
 *     ),
 *     @OA\Response(
 *         response=204,
 *         description="Role mis à jour avec succès"
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Role non trouvé"
 *     )
 * )
 */

    public function edit(int $id, Request $request): JsonResponse
{
    $Role = $this->repository->findOneBy(['id' => $id]);
    if ($Role){
        $Role = $this->serializer->deserialize(
            $request->getContent(),
            Role::class,
            'json',
            [AbstractNormalizer::OBJECT_TO_POPULATE => $Role]
        );
        $Role->setUpdateAt(new DateTimeImmutable());

        $this->manager->flush();

        return new JsonResponse(data: null, status: Response::HTTP_NO_CONTENT);
    }

    return new JsonResponse(data: null, status: Response::HTTP_NOT_FOUND);
    
}

    #[Route('/{id}',name:'delete', methods:'DELETE')]

/** 
 * @OA\Delete(
 *     path="/api/Role/{id}",
 *     summary="Supprimer un Role par son ID",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="ID du Role à supprimer",
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=204,
 *         description="Role supprimé avec succès"
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Role non trouvé"
 *     )
 * )
 */

    public function delete(int $id): JsonResponse
{
    $Role = $this->repository->findOneBy(['id' => $id]);
    if ($Role) {
        $this->manager->remove($Role);
        $this->manager->flush();

        return new JsonResponse(data: null, status: Response::HTTP_NO_CONTENT);
}
        

return new JsonResponse(data: null, status: Response::HTTP_NOT_FOUND);
}
}

