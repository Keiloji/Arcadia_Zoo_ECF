<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface; // Utiliser UserPasswordHasherInterface

#[Route('/api/signup', name: 'app_api_signup_')]
class SignupController extends AbstractController
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
    // Désérialiser les données reçues dans l'objet User
    $user = $this->serializer->deserialize($request->getContent(), User::class, 'json');

    // Encoder le mot de passe avant de le stocker
    $user->setPassword($this->passwordHasher->hashPassword($user, $user->getPassword())); // Changer la méthode pour utiliser hashPassword
    $user->setCreatedAt(new DateTimeImmutable());
    $user->setRoles($user->getRoles() ?: ['ROLE_USER']); // Assigner le rôle utilisateur par défaut

    // Persister l'utilisateur dans la base de données
    $this->manager->persist($user);
    $this->manager->flush();

    // Préparer la réponse
    $responseData = $this->serializer->serialize($user, 'json');
    $location = $this->generateUrl('app_api_signup_show', ['id' => $user->getId()], false); // Utilisez false au lieu de absolute: true

    return new JsonResponse($responseData, Response::HTTP_CREATED, ["Location" => $location], true);
}

    #[Route('/{id}',name: 'show', methods:'GET')]
    public function show(int $id): JsonResponse
    {
        $user = $this->repository->find($id);

        if ($user) {
            $responseData = $this->serializer->serialize($user, 'json');
            return new JsonResponse($responseData, Response::HTTP_OK, [], true);
        }

    return new JsonResponse(data: null, status: Response::HTTP_NOT_FOUND);

}

    #[Route('/{id}',name:'edit', methods:'PUT')]
    public function edit(int $id, Request $request): JsonResponse
{
    $Signup = $this->repository->findOneBy(['id' => $id]);
    if ($Signup){
        $Signup = $this->serializer->deserialize(
            $request->getContent(),
            Signup::class,
            'json',
            [AbstractNormalizer::OBJECT_TO_POPULATE => $Signup]
        );
        $Signup->setUpdateAt(new DateTimeImmutable());

        $this->manager->flush();

        return new JsonResponse(data: null, status: Response::HTTP_NO_CONTENT);
    }

    return new JsonResponse(data: null, status: Response::HTTP_NOT_FOUND);
    
}

    #[Route('/{id}',name:'delete', methods:'DELETE')]
    public function delete(int $id): JsonResponse
{
    $Signup = $this->repository->findOneBy(['id' => $id]);
    if ($Signup) {
        $this->manager->remove($Signup);
        $this->manager->flush();

        return new JsonResponse(data: null, status: Response::HTTP_NO_CONTENT);
}
        

return new JsonResponse(data: null, status: Response::HTTP_NOT_FOUND);
}
}

// Inclure les fonctions de sécurité
include 'includes/security.php'; 

// Filtrage d'une entrée utilisateur
$user_input = filter_input_user($_POST['input']);

// Connexion à la base de données
$pdo = get_db_connection();

// Exécuter une requête SQL de manière sécurisée
$result = execute_secure_query($pdo, "SELECT * FROM users WHERE username = :username", ['username' => $user_input]);

// Sécurisation d'une inclusion de fichier
secure_file_inclusion($_GET['page']);
?>