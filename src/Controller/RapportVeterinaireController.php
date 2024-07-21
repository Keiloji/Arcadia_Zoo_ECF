<?php

namespace App\Controller;

use App\Entity\RapportVeterinaire;
use App\Repository\RapportVeterinaireRepository;
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

#[Route('/api/RapportVeterinaire', name:'app_api_RapportVeterinaire_')]
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
    $RapportVeterinaire = $this->serializer->deserialize($request->getContent(), RapportVeterinaire::class, 'json');
    $RapportVeterinaire->setCreatedAt(new DateTimeImmutable());


    $this->manager->persist($RapportVeterinaire);
    $this->manager->flush();


    $responseData = $this->serializer->serialize($RapportVeterinaire, 'json');
    $location= $this->urlGenerator->generate(
        'app_api_RapportVeterinaire_show',
        ['id' => $RapportVeterinaire->getId()],
        referenceType: UrlGeneratorInterface::ABSOLUTE_URL,
    );

    return new JsonResponse($responseData, Response::HTTP_CREATED,["Location" => $location], true);

}

    #[Route('/{id}',name: 'show', methods:'GET')]
    public function show(int $id): JsonResponse
{
    $RapportVeterinaire= $this->repository->findOneBy(['id' => $id]);

    if ($RapportVeterinaire) {
        $responseData = $this->serializer->serialize($RapportVeterinaire, format: 'json');

        return new JsonResponse($responseData, Response::HTTP_OK, [], true);
    }

    return new JsonResponse(data: null, status: Response::HTTP_NOT_FOUND);

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

        return new JsonResponse(data: null, status: Response::HTTP_NO_CONTENT);
    }

    return new JsonResponse(data: null, status: Response::HTTP_NOT_FOUND);
    
}

    #[Route('/{id}',name:'delete', methods:'DELETE')]
    public function delete(int $id): JsonResponse
{
    $RapportVeterinaire = $this->repository->findOneBy(['id' => $id]);
    if ($RapportVeterinaire) {
        $this->manager->remove($RapportVeterinaire);
        $this->manager->flush();

        return new JsonResponse(data: null, status: Response::HTTP_NO_CONTENT);
}
        

return new JsonResponse(data: null, status: Response::HTTP_NOT_FOUND);
}
}
