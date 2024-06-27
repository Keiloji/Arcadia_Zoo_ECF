<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class NosServicesController extends AbstractController
{
    #[Route('/NosServices')]
    public function NosServices() : Response
    {
	return new Response ( content: 'Bienvenue sur vos Services !') ;
        }
}