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

        $user_email =  $this->getUser()->email;

        $songs = $entityManager->getRepository(Song::class)->findAll();

        $sum = 0;
        foreach ($songs as $a) {
            $sum = 0;
            $song_id = $a->getId();
            $repository = $entityManager->getRepository(Rate::class);

            $qb = $repository->createQueryBuilder('r')
                ->select('count(r.id)')
                ->where('r.song = :song_id')
                ->setParameter('song_id', $song_id);
            foreach($a->getRate() as $r){
                $sum += $r->getPoints();
            }
            $count = $qb->getQuery()->getSingleScalarResult();
            if ($count > 0) {
                $a->sum = $sum / $count;
            }else{
                $a->sum = $sum;
            }
        }

        return $this->render('songs/index.html.twig', [
            'songs' => $songs,
            'user' => $user,
            'user_email'=>$user_email,
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
            $this->addFlash('success',''.$song->getSongName().' from band: '.$song->getBand().' Submitted Successfully!');

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

        $vote->setPoints($request->request->get('vote'));
        $vote->setUserId($user->getId());
        $vote->setSong($song);


        $entityManager->persist($song);
        $entityManager->persist($vote);
        $entityManager->flush();
        $this->addFlash('success','You gave: '.$vote->getPoints() .'points to song- '.$song->getSongName());
        return $this->redirect($this->generateUrl('songs'));
    }
}
