<?php

namespace App\Controller;

use App\Entity\Livres;
use App\Form\LivresType;
use App\Repository\LivresRepository;
use App\Repository\CategorieRepository;
use Knp\Component\Pager\PaginatorInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/livres')]
class LivresController extends AbstractController
{
    #[Route('/afficher', name: 'app_livres_index', methods: ['GET'])]
    public function index(
        LivresRepository $livresRepository,
        CategorieRepository $categorieRepository,
        PaginatorInterface $paginator,
        Request $request
    ): Response {
        // Création du queryBuilder de base
        $queryBuilder = $livresRepository->createQueryBuilder('l')
            ->orderBy('l.titre', 'ASC');
    
        // Pagination des résultats
        $livres = $paginator->paginate(
            $queryBuilder->getQuery(), // La requête à paginer
            $request->query->getInt('page', 1), // Numéro de page
            12, // Nombre d'éléments par page
            [
                'defaultSortFieldName' => 'l.titre',
                'defaultSortDirection' => 'asc',
            ]
        );
    
        return $this->render('livres/index.html.twig', [
            'livres' => $livres,
            'categories' => $categorieRepository->findAll(),
            'searchParams' => [] // Tableau vide car pas de recherche dans cette action
        ]);
    }
    #[Route('/acceuil', name: 'app_livres_acceuil', methods: ['GET'])]
    public function acceuil(
        Request $request, 
        LivresRepository $livresRepository,
        CategorieRepository $categorieRepository,
        PaginatorInterface $paginator
    ): Response {
        $searchParams = [
            'titre' => trim($request->query->get('titre', '')),
            'editeur' => trim($request->query->get('editeur', '')),
            'categorie' => trim($request->query->get('categorie', ''))
        ];
    
        $queryBuilder = $livresRepository->createQueryBuilder('l')
            ->leftJoin('l.categorie', 'c')
            ->orderBy('l.titre', 'ASC');
    
        if (!empty($searchParams['titre'])) {
            $queryBuilder->andWhere('l.titre LIKE :titre')
                ->setParameter('titre', '%' . $searchParams['titre'] . '%');
        }
    
        if (!empty($searchParams['editeur'])) {
            $queryBuilder->andWhere('l.editeur LIKE :editeur')
                ->setParameter('editeur', '%' . $searchParams['editeur'] . '%');
        }
    
        if (!empty($searchParams['categorie'])) {
            $queryBuilder->andWhere('c.libelle = :categorie')
                ->setParameter('categorie', $searchParams['categorie']);
        }
    
        $livres = $paginator->paginate(
            $queryBuilder->getQuery(),
            $request->query->getInt('page', 1),
            12
        );
    
        return $this->render('livres/acceuil.html.twig', [
            'livres' => $livres,
            'categories' => $categorieRepository->findAll(),
            'searchParams' => $searchParams
        ]);
    }
    // ... [autres méthodes restent inchangées]

    #[Route('/new', name: 'app_livres_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $livre = new Livres();
        $form = $this->createForm(LivresType::class, $livre);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Gestion manuelle de la date
            $dateValue = $request->request->all()['livres']['dateEdition'] ?? null;
            if ($dateValue) {
                $date = \DateTime::createFromFormat('d/m/Y', $dateValue);
                if ($date) {
                    $livre->setDateEdition($date);
                }
            }

            $entityManager->persist($livre);
            $entityManager->flush();
            
            return $this->redirectToRoute('app_livres_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('livres/new.html.twig', [
            'livre' => $livre,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/edit', name: 'app_livres_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Livres $livre, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(LivresType::class, $livre);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Gestion manuelle de la date
            $dateValue = $request->request->all()['livres']['dateEdition'] ?? null;
            if ($dateValue) {
                $date = \DateTime::createFromFormat('d/m/Y', $dateValue);
                if ($date) {
                    $livre->setDateEdition($date);
                }
            }

            $entityManager->flush();
            return $this->redirectToRoute('app_livres_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('livres/edit.html.twig', [
            'livre' => $livre,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_livres_show', methods: ['GET'])]
public function show(Livres $livre, EntityManagerInterface $em): Response
{
    // Solution 1: Chargement explicite de la relation
    $livre = $em->getRepository(Livres::class)->find($livre->getId());
    // OU Solution 2: Utilisez une requête personnalisée
    $livre = $em->getRepository(Livres::class)->createQueryBuilder('l')
        ->leftJoin('l.categorie', 'c')
        ->addSelect('c') // Important pour charger la relation
        ->where('l.id = :id')
        ->setParameter('id', $livre->getId())
        ->getQuery()
        ->getOneOrNullResult();

    if (!$livre) {
        throw $this->createNotFoundException('Livre non trouvé');
    }

    return $this->render('livres/show.html.twig', [
        'livre' => $livre,
    ]);
}
    #[Route('/showClient/{id}', name: 'app_livres_showClient', methods: ['GET'])]
    public function showClient(Livres $livre): Response
    {
        return $this->render('livres/showClient.html.twig', [
            'livre' => $livre,
        ]);
    }

 

    #[Route('/{id}', name: 'app_livres_delete', methods: ['POST'])]
    public function delete(Request $request, Livres $livre, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$livre->getId(), $request->getPayload()->get('_token'))) {
            $entityManager->remove($livre);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_livres_index', [], Response::HTTP_SEE_OTHER);
    }
}