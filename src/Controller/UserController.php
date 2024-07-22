<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
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

#[Route('/api/User', name:'app_api_User_')]
class UserController extends AbstractController
{
    
    public function __construct(
        private EntityManagerInterface $manager, 
        private UserRepository $repository,
        private SerializerInterface $serializer,
        private UrlGeneratorInterface $urlGenerator,
        ) {
        
    }
    #[Route(methods:'POST')]
    public function new(Request $request): JsonResponse
{
    $User = $this->serializer->deserialize($request->getContent(), User::class, 'json');
    $User->setCreatedAt(new DateTimeImmutable());


    $this->manager->persist($User);
    $this->manager->flush();


    $responseData = $this->serializer->serialize($User, 'json');
    $location= $this->urlGenerator->generate(
        'app_api_User_show',
        ['id' => $User->getId()],
        referenceType: UrlGeneratorInterface::ABSOLUTE_URL,
    );

    return new JsonResponse($responseData, Response::HTTP_CREATED,["Location" => $location], true);

}

    #[Route('/{id}',name: 'show', methods:'GET')]
    public function show(int $id): JsonResponse
{
    $User= $this->repository->findOneBy(['id' => $id]);

    if ($User) {
        $responseData = $this->serializer->serialize($User, format: 'json');

        return new JsonResponse($responseData, Response::HTTP_OK, [], true);
    }

    return new JsonResponse(data: null, status: Response::HTTP_NOT_FOUND);

}

    #[Route('/{id}',name:'edit', methods:'PUT')]
    public function edit(int $id, Request $request): JsonResponse
{
    $User= $this->repository->findOneBy(['id' => $id]);
    if ($User){
        $Utilisateur= $this->serializer->deserialize(
            $request->getContent(),
            User::class,
            'json',
            [AbstractNormalizer::OBJECT_TO_POPULATE => $User]
        );
        $User->setUpdateAt(new DateTimeImmutable());

        $this->manager->flush();

        return new JsonResponse(data: null, status: Response::HTTP_NO_CONTENT);
    }

    return new JsonResponse(data: null, status: Response::HTTP_NOT_FOUND);
    
}

    #[Route('/{id}',name:'delete', methods:'DELETE')]
    public function delete(int $id): JsonResponse
{
    $User = $this->repository->findOneBy(['id' => $id]);
    if ($User) {
        $this->manager->remove($User);
        $this->manager->flush();

        return new JsonResponse(data: null, status: Response::HTTP_NO_CONTENT);
}
        

return new JsonResponse(data: null, status: Response::HTTP_NOT_FOUND);
}
}

