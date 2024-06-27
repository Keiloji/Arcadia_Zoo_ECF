<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SignupController extends AbstractController
{
    #[Route('/Signup')]
    public function Signup() : Response
    {
	return new Response ( content: 'Bienvenue sur la section Créer un compte !') ;
        }
}