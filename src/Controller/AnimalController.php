<?php

namespace App\Controller;

use App\Entity\Animal;
use App\Repository\AnimalRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
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

#[Route('/api/Animal', name:'app_api_Animal_')]
class AnimalController extends AbstractController
{
    
    public function __construct(
        private EntityManagerInterface $manager, 
        private AnimalRepository $repository,
        private SerializerInterface $serializer,
        private UrlGeneratorInterface $urlGenerator,
        ) {
        
    }
    #[Route(methods:'POST')]
    public function new(Request $request): JsonResponse
{
    $Animal = $this->serializer->deserialize($request->getContent(), Animal::class, 'json');
    $Animal->setCreatedAt(new DateTimeImmutable());


    $this->manager->persist($Animal);
    $this->manager->flush();


    $responseData = $this->serializer->serialize($Animal, 'json');
    $location= $this->urlGenerator->generate(
        'app_api_Animal_show',
        ['id' => $Animal->getId()],
        referenceType: UrlGeneratorInterface::ABSOLUTE_URL,
    );

    return new JsonResponse($responseData, Response::HTTP_CREATED,["Location" => $location], true);

}

    #[Route('/{id}',name: 'show', methods:'GET')]
    public function show(int $id): JsonResponse
{
    $Animal= $this->repository->findOneBy(['id' => $id]);

    if ($Animal) {
        $responseData = $this->serializer->serialize($Animal, format: 'json');

        return new JsonResponse($responseData, Response::HTTP_OK, [], true);
    }

    return new JsonResponse(data: null, status: Response::HTTP_NOT_FOUND);

}

    #[Route('/{id}',name:'edit', methods:'PUT')]
    public function edit(int $id, Request $request): JsonResponse
{
    $Animal= $this->repository->findOneBy(['id' => $id]);
    if ($Animal){
        $Animal= $this->serializer->deserialize(
            $request->getContent(),
            Animal::class,
            'json',
            [AbstractNormalizer::OBJECT_TO_POPULATE => $Animal]
        );
        $Animal->setUpdateAt(new DateTimeImmutable());

        $this->manager->flush();

        return new JsonResponse(data: null, status: Response::HTTP_NO_CONTENT);
    }

    return new JsonResponse(data: null, status: Response::HTTP_NOT_FOUND);
    
}

    #[Route('/{id}',name:'delete', methods:'DELETE')]
    public function delete(int $id): JsonResponse
{
    $Animal = $this->repository->findOneBy(['id' => $id]);
    if ($Animal) {
        $this->manager->remove($Animal);
        $this->manager->flush();

        return new JsonResponse(data: null, status: Response::HTTP_NO_CONTENT);
}
        

return new JsonResponse(data: null, status: Response::HTTP_NOT_FOUND);
}
}
