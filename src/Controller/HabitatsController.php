<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HabitatsController extends AbstractController
{
    #[Route('/Habiats')]
    public function Habitats() : Response
    {
	return new Response ( content: 'Bienvenue sur la section Habitat !') ;
        }
}