<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SongsController extends AbstractController
{
    #[Route('/songs', name: 'app_songs')]
    public function index(): Response
    {
        return $this->render('songs/index.html.twig', [
            'controller_name' => 'SongsController',
        ]);
    }
}
