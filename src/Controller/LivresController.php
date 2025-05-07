<?php

namespace App\Controller;

use App\Entity\Livres;
use App\Form\LivresType;
use App\Repository\LivresRepository;
use App\Controller\CategorieRepository;
use App\Repository\CategorieRepository as RepositoryCategorieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/livres')]
final class LivresController extends AbstractController
{
    #[Route('/afficher',name: 'app_livres_index', methods: ['GET'])]
    public function index(LivresRepository $livresRepository): Response
    {
        return $this->render('livres/index.html.twig', [
            'livres' => $livresRepository->findAll(),
        ]);
    }

    #[Route('/acceuil',name: 'app_livres_acceuil', methods: ['GET'])]
    public function index2(LivresRepository $livresRepository): Response
    {
        return $this->render('livres/acceuil.html.twig', [
            'livres' => $livresRepository->findAll(),
        ]);
    }
    #[Route('/acceuil', name: 'app_livres_acceuil2', methods: ['GET'])]
public function index3(
    Request $request, 
    LivresRepository $livresRepository,
    RepositoryCategorieRepository $categorieRepository
): Response
{
    // Récupérer les paramètres de recherche
    $titre = $request->query->get('titre');
    $editeur = $request->query->get('editeur');
    $categorieId = $request->query->get('categorie');

    // Utiliser la méthode de recherche si des filtres sont présents
    if ($titre || $editeur || $categorieId) {
        $livres = $livresRepository->searchByFilters($titre, $editeur, $categorieId);
    } else {
        $livres = $livresRepository->findAll();
    }

    return $this->render('livres/acceuil.html.twig', [
        'livres' => $livres,
        'categories' => $categorieRepository->findAll(),
        'searchParams' => [
            'titre' => $titre,
            'editeur' => $editeur,
            'categorie' => $categorieId
        ]
    ]);
}

    #[Route('/new', name: 'app_livres_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $livre = new Livres();
        $form = $this->createForm(LivresType::class, $livre);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($livre);
            $entityManager->flush();
            
            return $this->redirectToRoute('app_livres_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('livres/new.html.twig', [
            'livre' => $livre,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_livres_show', methods: ['GET'])]
    public function show(Livres $livre): Response
    {
        return $this->render('livres/show.html.twig', [
            'livre' => $livre,
        ]);
    }
    #[Route('shoxClient/{id}', name: 'app_livres_showClient', methods: ['GET'])]
    public function show2(Livres $livre): Response
    {
        return $this->render('livres/showClient.html.twig', [
            'livre' => $livre,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_livres_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Livres $livre, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(LivresType::class, $livre);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_livres_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('livres/edit.html.twig', [
            'livre' => $livre,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_livres_delete', methods: ['POST'])]
    public function delete(Request $request, Livres $livre, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$livre->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($livre);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_livres_index', [], Response::HTTP_SEE_OTHER);
    }
}


