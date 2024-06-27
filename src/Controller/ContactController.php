<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ContactController extends AbstractController
{
    #[Route('/Contact')]
    public function Contact() : Response
    {
	return new Response ( content: 'Bienvenue sur la section Contact !') ;
        }
}