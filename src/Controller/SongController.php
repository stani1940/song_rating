<?php

namespace App\Controller;

use App\Entity\Rate;
use App\Entity\Song;
use App\Form\SongType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SongController extends AbstractController
{
    /**
     * @Route("/songs", name="songs")
     */
    public function index(EntityManagerInterface $entityManager): Response
    {
        $user = get_current_user();
        $songs = $entityManager->getRepository(Song::class)->findAll();

        $sum = 0;
        foreach ($songs as $a) {
            $sum = 0;
            foreach($a->getRate() as $r){
                $sum += $r->getPoints();
            }
            $a->sum = $sum;
        }
        return $this->render('songs/index.html.twig', [
            'songs' => $songs,
            'user' => $user
        ]);
    }
    /**
     * @Route("/songs/create",name="create")
     * @throws \Doctrine\ORM\ORMException
     */

    public function create(EntityManagerInterface $entityManager,Request $request,)
    {
        $song = new Song();
        $form = $this->createForm(SongType::class, $song);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){

            $entityManager->persist($song);
            $entityManager->flush();
            $this->addFlash('notice',''.$song->getSongName().' from band: '.$song->getBand().' Submitted Successfully!');

            return $this->redirectToRoute('songs');
        }
        return $this->render('songs/create.html.twig',[
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/songs/{id}/vote", name="app_song_vote", methods="POST")
     */
    public function songVote(Song $song, Request $request, EntityManagerInterface $entityManager)
    {
        $user = $this->getUser();
        //  dd($user->getId());
        // $user = get_current_user();
        $vote = new Rate();
        $total = 100;
        $vote->setPoints($request->request->get('vote'));
        $vote->setUserId($user->getId());
        $vote->setSong($song);

        $entityManager->persist($song);
        $entityManager->persist($vote);
        $entityManager->flush();

        return $this->redirect($this->generateUrl('songs'));
        // return new Response('You gave: '.' '.$vote.' points to songs- '.$songs->getName());
    }
}
