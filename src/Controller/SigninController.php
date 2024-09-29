<?php

namespace App\Controller;

use App\Entity\User; // Assurez-vous que vous avez une entité User
use App\Repository\UserRepository; // Le repository de User
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/api/signin', name:'app_api_signin_')]
class SigninController extends AbstractController
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
    $Signin = $this->serializer->deserialize($request->getContent(), Signin::class, 'json');
    $Signin->setCreatedAt(new DateTimeImmutable());


    $this->manager->persist($Signin);
    $this->manager->flush();


    $responseData = $this->serializer->serialize($Signin, 'json');
    $location= $this->urlGenerator->generate(
        'app_api_Signin_show',
        ['id' => $Signin->getId()],
        referenceType: UrlGeneratorInterface::ABSOLUTE_URL,
    );

    return new JsonResponse($responseData, Response::HTTP_CREATED,["Location" => $location], true);

}

    #[Route('/{id}',name: 'show', methods:'GET')]
    public function show(int $id): JsonResponse
{
    $Signin = $this->repository->findOneBy(['id' => $id]);

    if ($Signin) {
        $responseData = $this->serializer->serialize($Signin, format: 'json');

        return new JsonResponse($responseData, Response::HTTP_OK, [], true);
    }

    return new JsonResponse(data: null, status: Response::HTTP_NOT_FOUND);

}

    #[Route('/{id}',name:'edit', methods:'PUT')]
    public function edit(int $id, Request $request): JsonResponse
{
    $Signin = $this->repository->findOneBy(['id' => $id]);
    if ($Signin){
        $Signin= $this->serializer->deserialize(
            $request->getContent(),
            Signin::class,
            'json',
            [AbstractNormalizer::OBJECT_TO_POPULATE => $Signin]
        );
        $Signin->setUpdateAt(new DateTimeImmutable());

        $this->manager->flush();

        return new JsonResponse(data: null, status: Response::HTTP_NO_CONTENT);
    }

    return new JsonResponse(data: null, status: Response::HTTP_NOT_FOUND);
    
}

    #[Route('/{id}',name:'delete', methods:'DELETE')]
    public function delete(int $id): JsonResponse
{
    $Signin = $this->repository->findOneBy(['id' => $id]);
    if ($Signin) {
        $this->manager->remove($Signin);
        $this->manager->flush();

        return new JsonResponse(data: null, status: Response::HTTP_NO_CONTENT);
}
        

return new JsonResponse(data: null, status: Response::HTTP_NOT_FOUND);
}
}
