<?php

namespace App\Controller;

use App\Entity\Livres;
use App\Repository\LivresRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class LivresController extends AbstractController
{
    #[Route('/livres/create', name: 'app_livres_create')]
    public function create(EntityManagerInterface $em): Response
    {
      $livre=new Livres();
      $date =new \DateTime("2025-01-01");
      $livre->setTitre("Titre1")
      ->setisbn("111-111-111")
      ->setresume("xrfcgvhbjnk,lgcvhb")->setEditeur("Eyrolles")->setDateEdition($date)->setImage("https://picsum.photos/150/id=4")
    ->setPrix(120)->setSlug("titre-1") ;//creer un objet
    $em->persist($livre);
    $em->flush();
    return new Response ('created new book with name : '.$livre->getTitre());
    }
    #[Route('/livres/show/{id<\d+>}', name: 'app_livres_show')]
    public function show(LivresRepository $rep,$id): Response
    {
       $livre=$rep->find($id);
       if(!$livre){
        throw $this->createNotFoundException('livre ayant id:' .$id. ' n existe pas');
       }
       dd($livre);
    }
    #[Route('/livres/show2', name: 'app_livres_show2')]
    public function show2(LivresRepository $rep): Response
    {
       $livre=$rep->findOneBy(['titre' =>'titre1']);
       dd($livre);
    }
    #[Route('/livres/show3', name: 'app_livres_show3')]
    public function show3(LivresRepository $rep): Response
    {
       $livre=$rep->findBy(['titre' =>'titre1','editeur'=>'Dyno'],['prix'=>'ASC']);//affichage du prix selon ordere croissant
       dd($livre);
    }//tous les livres ayant le titre1
}
