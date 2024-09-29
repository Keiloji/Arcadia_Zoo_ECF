<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Repository\ContactRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use OpenApi\Annotations as OA;
use App\Service\JwtService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/api/contact', name: 'app_api_contact_')]
class ContactController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $manager,
        private ContactRepository $repository,
        private SerializerInterface $serializer,
        private UrlGeneratorInterface $urlGenerator
    ) {}

    #[Route('', name: 'create', methods: ['POST'])]

    /**
     * @OA\Post(
     *     path="/api/contact",
     *     summary="Créer un contact",
     *     @OA\RequestBody(
     *         required=true,
     *         description="Données du contact à créer",
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", example="Nom du contact"),
     *             @OA\Property(property="description", type="string", example="Description du contact")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Contact créé avec succès",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="name", type="string", example="Nom du contact"),
     *             @OA\Property(property="description", type="string", example="Description du contact"),
     *             @OA\Property(property="createdAt", type="string", format="date-time")
     *         )
     *     )
     * )
     */
    public function new(Request $request): JsonResponse
    {
        $contact = $this->serializer->deserialize($request->getContent(), Contact::class, 'json');
        $contact->setCreatedAt(new DateTimeImmutable());

        $this->manager->persist($contact);
        $this->manager->flush();

        $responseData = $this->serializer->serialize($contact, 'json');
        $location = $this->urlGenerator->generate(
            'app_api_contact_show',
            ['id' => $contact->getId()],
            UrlGeneratorInterface::ABSOLUTE_URL
        );

        return new JsonResponse($responseData, Response::HTTP_CREATED, ["Location" => $location], true);
    }

    #[Route('/{id}', name: 'show', methods: ['GET'])]

    /**
     * @OA\Get(
     *     path="/api/contact/{id}",
     *     summary="Récupérer un contact par son ID",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID du contact",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Détails du contact",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="name", type="string", example="Nom du contact"),
     *             @OA\Property(property="description", type="string", example="Description du contact"),
     *             @OA\Property(property="createdAt", type="string", format="date-time")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Contact non trouvé"
     *     )
     * )
     */
    public function show(int $id): JsonResponse
    {
        $contact = $this->repository->findOneBy(['id' => $id]);

        if ($contact) {
            $responseData = $this->serializer->serialize($contact, 'json');

            return new JsonResponse($responseData, Response::HTTP_OK, [], true);
        }

        return new JsonResponse(null, Response::HTTP_NOT_FOUND);
    }

    #[Route('/{id}', name: 'edit', methods: ['PUT'])]

    /**
     * @OA\Put(
     *     path="/api/contact/{id}",
     *     summary="Mettre à jour un contact par son ID",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID du contact à mettre à jour",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="name", type="string", example="Nom du contact"),
     *             @OA\Property(property="description", type="string", example="Description du contact")
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
        $contact = $this->repository->findOneBy(['id' => $id]);

        if ($contact) {
            $this->serializer->deserialize(
                $request->getContent(),
                Contact::class,
                'json',
                [AbstractNormalizer::OBJECT_TO_POPULATE => $contact]
            );
            $contact->setUpdatedAt(new DateTimeImmutable());

            $this->manager->flush();

            return new JsonResponse(null, Response::HTTP_NO_CONTENT);
        }

        return new JsonResponse(null, Response::HTTP_NOT_FOUND);
    }

    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]

    /**
     * @OA\Delete(
     *     path="/api/contact/{id}",
     *     summary="Supprimer un contact par son ID",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID du contact à supprimer",
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
        $contact = $this->repository->findOneBy(['id' => $id]);

        if ($contact) {
            $this->manager->remove($contact);
            $this->manager->flush();

            return new JsonResponse(null, Response::HTTP_NO_CONTENT);
        }

        return new JsonResponse(null, Response::HTTP_NOT_FOUND);
    }
}