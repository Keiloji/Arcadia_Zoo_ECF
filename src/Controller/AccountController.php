<?php

namespace App\Controller;

use App\Entity\Account;
use App\Repository\AccountRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use OpenApi\Annotations as OA;
use Exception;
use App\Service\JwtService;
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
    private JwtService $jwtService;

    public function __construct(
        private EntityManagerInterface $manager, 
        private AccountRepository $repository,
        private SerializerInterface $serializer,
        private UrlGeneratorInterface $urlGenerator,
        JwtService $jwtService 
    ) { $this->jwtService = $jwtService; // <-- Initialiser la propriété
}
    #[Route('/create', name: 'create', methods: ['POST'])]
    /** 
     * @OA\Post(
     *     path="/api/Account/create",
     *     summary="Créer un nouveau compte",
     *     @OA\RequestBody(
     *         required=true,
     *         description="Données du compte à créer",
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", example="Nom du compte"),
     *             @OA\Property(property="description", type="string", example="Description du compte")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Compte créé avec succès",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="name", type="string", example="Nom du compte"),
     *             @OA\Property(property="description", type="string", example="Description du compte"),
     *             @OA\Property(property="createdAt", type="string", format="date-time")
     *         )
     *     )
     * )
     */
    public function new(Request $request): JsonResponse
    {
        $account = $this->serializer->deserialize($request->getContent(), Account::class, 'json');
        $account->setCreatedAt(new DateTimeImmutable());

        $this->manager->persist($account);
        $this->manager->flush();

        $responseData = $this->serializer->serialize($account, 'json');
        $location = $this->urlGenerator->generate('app_api_Account_show', ['id' => $account->getId()], UrlGeneratorInterface::ABSOLUTE_URL);

        return new JsonResponse($responseData, Response::HTTP_CREATED, ["Location" => $location], true);
    }

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    /** 
     * @OA\Get(
     *     path="/api/Account/{id}",
     *     summary="Afficher un compte par son ID",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID du compte à afficher",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Compte trouvé",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="name", type="string", example="Nom du compte"),
     *             @OA\Property(property="description", type="string", example="Description du compte")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Compte non trouvé"
     *     )
     * )
     */
    public function show(int $id): JsonResponse
    {
        $account = $this->repository->findOneBy(['id' => $id]);

        if ($account) {
            $responseData = $this->serializer->serialize($account, 'json');

            return new JsonResponse($responseData, Response::HTTP_OK, [], true);
        }

        return new JsonResponse(null, Response::HTTP_NOT_FOUND);
    }

    #[Route('/{id}', name:'edit', methods: ['PUT'])]
    /** 
     * @OA\Put(
     *     path="/api/Account/{id}",
     *     summary="Mettre à jour un compte par son ID",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID du compte à mettre à jour",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="name", type="string", example="Nom du compte"),
     *             @OA\Property(property="description", type="string", example="Description du compte")
     *         )
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Compte mis à jour avec succès"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Compte non trouvé"
     *     )
     * )
     */
    public function edit(int $id, Request $request): JsonResponse
    {
        $account = $this->repository->findOneBy(['id' => $id]);

        if ($account) {
            $this->serializer->deserialize($request->getContent(), Account::class, 'json', [AbstractNormalizer::OBJECT_TO_POPULATE => $account]);
            $account->setUpdatedAt(new DateTimeImmutable());

            $this->manager->flush();

            return new JsonResponse(null, Response::HTTP_NO_CONTENT);
        }

        return new JsonResponse(null, Response::HTTP_NOT_FOUND);
    }

    #[Route('/{id}', name:'delete', methods: ['DELETE'])]
    /** 
     * @OA\Delete(
     *     path="/api/Account/{id}",
     *     summary="Supprimer un compte par son ID",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID du compte à supprimer",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Compte supprimé avec succès"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Compte non trouvé"
     *     )
     * )
     */
    public function delete(int $id): JsonResponse
    {
        $account = $this->repository->findOneBy(['id' => $id]);

        if ($account) {
            $this->manager->remove($account);
            $this->manager->flush();

            return new JsonResponse(null, Response::HTTP_NO_CONTENT);
        }

        return new JsonResponse(null, Response::HTTP_NOT_FOUND);
    }
}