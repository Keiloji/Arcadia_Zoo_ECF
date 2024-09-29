<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Repository\ContactRepository;
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

#[Route('/api/Contact', name:'app_api_Contact_')]
class ContactController extends AbstractController
{
    //$Contact->setUtilisateur($this-getUser()));
    
    public function __construct(
        private EntityManagerInterface $manager, 
        private ContactRepository $repository,
        private SerializerInterface $serializer,
        private UrlGeneratorInterface $urlGenerator,
        ) {
        
    }
    #[Route(methods:'POST')]


    /** @OA\Get(
     *     path="/api/Contact",
     *     summary="Créer un Contact",
     *     @OA\RequestBody(
     *         required=true,
     *         description="Donner du Contact à créer",
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", example="Nom du Contact"),
     *             @OA\Property(property="description", type="string", example="Description du Contact")
     *         )
     * 
     *     ),
     *     @OA\Response(
     *         response=261,
     *         description="Contact crée avec succès",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="name", type="string", example="Nom du Contact"),
     *             @OA\Property(property="description", type="string", example="Description Contact"),
     *             @OA\Property(property="createdAt", type="string", format="date-time")
     *         )
     *     )
     *     )
     */

    public function new(Request $request): JsonResponse
{
    $Contact = $this->serializer->deserialize($request->getContent(), Contact::class, 'json');
    $Contact->setCreatedAt(new DateTimeImmutable());


    $this->manager->persist($Contact);
    $this->manager->flush();


    $responseData = $this->serializer->serialize($Contact, 'json');
    $location= $this->urlGenerator->generate(
        'app_api_Contact_show',
        ['id' => $Contact->getId()],
        referenceType: UrlGeneratorInterface::ABSOLUTE_URL,
    );

    return new JsonResponse($responseData, Response::HTTP_CREATED,["Location" => $location], true);

}

    #[Route('/{id}',name: 'show', methods:'GET')]

/** 
 * @OA\Put(
 *     path="/api/Contact/{id}",
 *     summary="Mettre à jour un Contact par son ID",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="ID du Contact à mettre à jour",
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="name", type="string", example="Nom du Contact"),
 *             @OA\Property(property="description", type="string", example="Description du Contact")
 *         )
 *     ),
 *     @OA\Response(
 *         response=204,
 *         description="Contact mis à jour avec succès"
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Contact non trouvé"
 *     )
 * )
 */

    public function show(int $id): JsonResponse
{
    $Contact = $this->repository->findOneBy(['id' => $id]);

    if ($Contact) {
        $responseData = $this->serializer->serialize($Contact, format: 'json');

        return new JsonResponse($responseData, Response::HTTP_OK, [], true);
    }

    return new JsonResponse(data: null, status: Response::HTTP_NOT_FOUND);

}

    #[Route('/{id}',name:'edit', methods:'PUT')]

/** 
 * @OA\Put(
 *     path="/api/Contact/{id}",
 *     summary="Mettre à jour un Contact par son ID",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="ID du Contact à mettre à jour",
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="name", type="string", example="Nom du Contact"),
 *             @OA\Property(property="description", type="string", example="Description du Contact")
 *         )
 *     ),
 *     @OA\Response(
 *         response=204,
 *         description="Contact mis à jour avec succès"
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Contact non trouvé"
 *     )
 * )
 */

    public function edit(int $id, Request $request): JsonResponse
{
    $Contact = $this->repository->findOneBy(['id' => $id]);
    if ($Contact){
        $Habitat= $this->serializer->deserialize(
            $request->getContent(),
            Contact::class,
            'json',
            [AbstractNormalizer::OBJECT_TO_POPULATE => $Contact]
        );
        $Contact->setUpdateAt(new DateTimeImmutable());

        $this->manager->flush();

        return new JsonResponse(data: null, status: Response::HTTP_NO_CONTENT);
    }

    return new JsonResponse(data: null, status: Response::HTTP_NOT_FOUND);
    
}

    #[Route('/{id}',name:'delete', methods:'DELETE')]

/** 
 * @OA\Delete(
 *     path="/api/Contact/{id}",
 *     summary="Supprimer un Contact par son ID",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="ID du Contact à supprimer",
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=204,
 *         description="Contact supprimé avec succès"
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Contact non trouvé"
 *     )
 * )
 */

    public function delete(int $id): JsonResponse
{
    $Contact = $this->repository->findOneBy(['id' => $id]);
    if ($Contact) {
        $this->manager->remove($Contact);
        $this->manager->flush();

        return new JsonResponse(data: null, status: Response::HTTP_NO_CONTENT);
}
        

return new JsonResponse(data: null, status: Response::HTTP_NOT_FOUND);
}
}

