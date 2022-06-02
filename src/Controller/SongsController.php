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
        $songs = ['Joni Be', 'Dilemma', 'Ostavame', 'Ti'];
        return $this->render('songs/index.html.twig', [
            'title' => 'Songs List',
            'songs' => $songs,
        ]);
    }
}
