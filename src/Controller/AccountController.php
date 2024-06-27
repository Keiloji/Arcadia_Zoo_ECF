<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AccountController extends AbstractController
{
    #[Route('/Account')]
    public function Account() : Response
    {
	return new Response ( content: 'Bienvenue sur la section Mon compte  !') ;
        }
}