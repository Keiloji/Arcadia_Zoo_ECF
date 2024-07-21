<?php

namespace App\Controller;

use App\Entity\Habitat;
use App\Repository\HabitatRepository;
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

#[Route('/api/Habitat', name:'app_api_Habitat_')]
class HabitatController extends AbstractController
{
    
    public function __construct(
        private EntityManagerInterface $manager, 
        private HabitatRepository $repository,
        private SerializerInterface $serializer,
        private UrlGeneratorInterface $urlGenerator,
        ) {
        
    }
    #[Route(methods:'POST')]
    public function new(Request $request): JsonResponse
{
    $Habitat = $this->serializer->deserialize($request->getContent(), Habitat::class, 'json');
    $Habitat->setCreatedAt(new DateTimeImmutable());


    $this->manager->persist($Habitat);
    $this->manager->flush();


    $responseData = $this->serializer->serialize($Habitat, 'json');
    $location= $this->urlGenerator->generate(
        'app_api_Habitat_show',
        ['id' => $Habitat->getId()],
        referenceType: UrlGeneratorInterface::ABSOLUTE_URL,
    );

    return new JsonResponse($responseData, Response::HTTP_CREATED,["Location" => $location], true);

}

    #[Route('/{id}',name: 'show', methods:'GET')]
    public function show(int $id): JsonResponse
{
    $Habitat= $this->repository->findOneBy(['id' => $id]);

    if ($Habitat) {
        $responseData = $this->serializer->serialize($Habitat, format: 'json');

        return new JsonResponse($responseData, Response::HTTP_OK, [], true);
    }

    return new JsonResponse(data: null, status: Response::HTTP_NOT_FOUND);

}

    #[Route('/{id}',name:'edit', methods:'PUT')]
    public function edit(int $id, Request $request): JsonResponse
{
    $Habitat = $this->repository->findOneBy(['id' => $id]);
    if ($Habitat){
        $Habitat= $this->serializer->deserialize(
            $request->getContent(),
            Habitat::class,
            'json',
            [AbstractNormalizer::OBJECT_TO_POPULATE => $Habitat]
        );
        $Habitat->setUpdateAt(new DateTimeImmutable());

        $this->manager->flush();

        return new JsonResponse(data: null, status: Response::HTTP_NO_CONTENT);
    }

    return new JsonResponse(data: null, status: Response::HTTP_NOT_FOUND);
    
}

    #[Route('/{id}',name:'delete', methods:'DELETE')]
    public function delete(int $id): JsonResponse
{
    $Habitat = $this->repository->findOneBy(['id' => $id]);
    if ($Habitat) {
        $this->manager->remove($Habitat);
        $this->manager->flush();

        return new JsonResponse(data: null, status: Response::HTTP_NO_CONTENT);
}
        

return new JsonResponse(data: null, status: Response::HTTP_NOT_FOUND);
}
}
