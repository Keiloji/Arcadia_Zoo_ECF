<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SigninController extends AbstractController
{
    #[Route('/Signin')]
    public function Signin() : Response
    {
	return new Response ( content: 'Bienvenue sur la section Connexion !') ;
        }
}