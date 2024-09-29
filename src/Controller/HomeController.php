<?php

namespace App\Controller;

use App\Entity\Home;
use App\Repository\HomeRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/api/Home', name: 'app_api_Home_')]
class HomeController extends AbstractController
{
    
    public function __construct(
        private EntityManagerInterface $manager,
        private HomeRepository $repository,
        private SerializerInterface $serializer,
        private UrlGeneratorInterface $urlGenerator,
        ) {
        
    }
    #[Route(methods:'POST')]
    public function new(Request $request): JsonResponse
    {
        $home = $this->serializer->deserialize($request->getContent(), Home::class, 'json');
        $home->setCreatedAt(new DateTimeImmutable());

        $this->manager->persist($home);
        $this->manager->flush();

        $responseData = $this->serializer->serialize($home, 'json');
        $location = $this->urlGenerator->generate(
            'app_api_Home_show',
            ['id' => $home->getId()],
            UrlGeneratorInterface::ABSOLUTE_URL
        );

        return new JsonResponse($responseData, Response::HTTP_CREATED, ['Location' => $location], true);
    }

    #[Route('/{id}',name: 'show', methods:'GET')]
    public function show(int $id): JsonResponse
    {
        $home = $this->repository->find($id);

        if ($home) {
            $responseData = $this->serializer->serialize($home, 'json');
            return new JsonResponse($responseData, Response::HTTP_OK, [], true);
        }

        return new JsonResponse(null, Response::HTTP_NOT_FOUND);
    }

    #[Route('/{id}',name:'edit', methods:'PUT')]
    public function edit(int $id, Request $request): JsonResponse
    {
        $home = $this->repository->find($id);

        if ($home) {
            $this->serializer->deserialize(
                $request->getContent(),
                Home::class,
                'json',
                [AbstractNormalizer::OBJECT_TO_POPULATE => $home]
            );
            $home->setUpdatedAt(new DateTimeImmutable());

            $this->manager->flush();

            return new JsonResponse(null, Response::HTTP_NO_CONTENT);
        }

        return new JsonResponse(null, Response::HTTP_NOT_FOUND);
    }

    #[Route('/{id}',name:'delete', methods:'DELETE')]
    public function delete(int $id): JsonResponse
    {
        $home = $this->repository->find($id);

        if ($home) {
            $this->manager->remove($home);
            $this->manager->flush();

            return new JsonResponse(null, Response::HTTP_NO_CONTENT);
        }

        return new JsonResponse(null, Response::HTTP_NOT_FOUND);
    }
}