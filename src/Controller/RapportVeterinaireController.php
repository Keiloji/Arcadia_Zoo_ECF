<?php

namespace App\Controller;

use App\Entity\RapportVeterinaire;
use App\Repository\RapportVeterinaireRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/api/rapport_veterinaire', name: 'app_api_rapport_veterinaire_')]
class RapportVeterinaireController extends AbstractController
{
    
    public function __construct(
        private EntityManagerInterface $manager,
        private RapportVeterinaireRepository $repository,
        private SerializerInterface $serializer,
        private UrlGeneratorInterface $urlGenerator,
    ) {
    }
    #[Route(methods:'POST')]
    public function new(Request $request): JsonResponse
    {
        $rapport = $this->serializer->deserialize($request->getContent(), RapportVeterinaire::class, 'json');
        $rapport->setCreatedAt(new DateTimeImmutable());

        $this->manager->persist($rapport);
        $this->manager->flush();

        $responseData = $this->serializer->serialize($rapport, 'json');
        $location = $this->urlGenerator->generate(
            'app_api_rapport_veterinaire_show',
            ['id' => $rapport->getId()],
            UrlGeneratorInterface::ABSOLUTE_URL
        );

        return new JsonResponse($responseData, Response::HTTP_CREATED, ["Location" => $location], true);
    }

    #[Route('/{id}',name: 'show', methods:'GET')]
    public function show(int $id): JsonResponse
{
    $RapportVeterinaire= $this->repository->findOneBy(['id' => $id]);

        if ($rapport) {
            $responseData = $this->serializer->serialize($rapport, 'json');

            return new JsonResponse($responseData, Response::HTTP_OK, [], true);
        }

        return new JsonResponse(null, Response::HTTP_NOT_FOUND);
    }

    #[Route('/{id}',name:'edit', methods:'PUT')]
    public function edit(int $id, Request $request): JsonResponse
{
    $RapportVeterinaire= $this->repository->findOneBy(['id' => $id]);
    if ($RapportVeterinaire){
        $RapportVeterinaire= $this->serializer->deserialize(
            $request->getContent(),
            RapportVeterinaire::class,
            'json',
            [AbstractNormalizer::OBJECT_TO_POPULATE => $RapportVeterinaire]
        );
        $RapportVeterinaire->setUpdateAt(new DateTimeImmutable());

            $this->manager->flush();

            return new JsonResponse(null, Response::HTTP_NO_CONTENT);
        }

        return new JsonResponse(null, Response::HTTP_NOT_FOUND);
    }

    #[Route('/{id}',name:'delete', methods:'DELETE')]
    public function delete(int $id): JsonResponse
    {
        $rapport = $this->repository->findOneBy(['id' => $id]);

        if ($rapport) {
            $this->manager->remove($rapport);
            $this->manager->flush();

            return new JsonResponse(null, Response::HTTP_NO_CONTENT);
        }

        return new JsonResponse(null, Response::HTTP_NOT_FOUND);
    }
}