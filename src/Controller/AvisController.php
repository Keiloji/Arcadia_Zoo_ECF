<?php

namespace App\Controller;

use App\Entity\Avis;
use App\Repository\AvisRepository;
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

#[Route('/api/Avis', name:'app_api_Avis_')]
class AvisController extends AbstractController
{
    
    public function __construct(
        private EntityManagerInterface $manager, 
        private AvisRepository $repository,
        private SerializerInterface $serializer,
        private UrlGeneratorInterface $urlGenerator,
        ) {
        
    }
    #[Route(methods:'POST')]
    public function new(Request $request): JsonResponse
{
    $Avis = $this->serializer->deserialize($request->getContent(), Avis::class, 'json');
    $Avis->setCreatedAt(new DateTimeImmutable());


    $this->manager->persist($Avis);
    $this->manager->flush();


    $responseData = $this->serializer->serialize($Avis, 'json');
    $location= $this->urlGenerator->generate(
        'app_api_Avis_show',
        ['id' => $Avis->getId()],
        referenceType: UrlGeneratorInterface::ABSOLUTE_URL,
    );

    return new JsonResponse($responseData, Response::HTTP_CREATED,["Location" => $location], true);

}

    #[Route('/{id}',name: 'show', methods:'GET')]
    public function show(int $id): JsonResponse
{
    $Avis= $this->repository->findOneBy(['id' => $id]);

    if ($Avis) {
        $responseData = $this->serializer->serialize($Avis, format: 'json');

        return new JsonResponse($responseData, Response::HTTP_OK, [], true);
    }

    return new JsonResponse(data: null, status: Response::HTTP_NOT_FOUND);

}

    #[Route('/{id}',name:'edit', methods:'PUT')]
    public function edit(int $id, Request $request): JsonResponse
{
    $Avis= $this->repository->findOneBy(['id' => $id]);
    if ($Avis){
        $Avis= $this->serializer->deserialize(
            $request->getContent(),
            Avis::class,
            'json',
            [AbstractNormalizer::OBJECT_TO_POPULATE => $Avis]
        );
        $Avis->setUpdateAt(new DateTimeImmutable());

        $this->manager->flush();

        return new JsonResponse(data: null, status: Response::HTTP_NO_CONTENT);
    }

    return new JsonResponse(data: null, status: Response::HTTP_NOT_FOUND);
    
}

    #[Route('/{id}',name:'delete', methods:'DELETE')]
    public function delete(int $id): JsonResponse
{
    $Avis = $this->repository->findOneBy(['id' => $id]);
    if ($Avis) {
        $this->manager->remove($Avis);
        $this->manager->flush();

        return new JsonResponse(data: null, status: Response::HTTP_NO_CONTENT);
}
        

return new JsonResponse(data: null, status: Response::HTTP_NOT_FOUND);
}
}
