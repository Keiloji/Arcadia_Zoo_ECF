<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Flex\Response as FlexResponse;

#[Route('api/zoo',name:'app_api_zoo_')]
class ZooController extends AbstractController
{
    #[Route(name:'new',methods:'POST')]
    public function new(): Response
{
}
    #[Route('/',name: 'show', methods:'GET')]
    public function show(): Response
{
    return $this->json(['message'=> 'Zoo de ma BDD']);
}
    #[Route('/',name:'edit', methods:'PUT')]
    public function edit(): Response
{
}
    #[Route('/',name:'delete', methods:'DELETE')]
    public function delete(): Response
{
}
}

