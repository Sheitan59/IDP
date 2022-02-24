<?php

namespace App\Controller;

use App\Classe\Search;
use App\Entity\Produits;
use App\Form\SearchType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class ProduitsController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }
    
    #[Route('/produits', name: 'produits')]
    public function index(Request $request): Response
    {
        $search = new Search();
        $form = $this->createForm(SearchType::class,$search);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $produits = $this->entityManager->getRepository(Produits::class)->findWithSearch($search);
        } else {
            $produits = $this->entityManager->getRepository(Produits::class)->findAll();
        }
        return $this->render('produits/index.html.twig', [
            'produits' => $produits,
            'form' => $form->createView()
        ]);
    }

    #[Route('/produit/{slug}', name: 'produit')]
    public function show($slug): Response
    {

        $produit = $this->entityManager->getRepository(Produits::class)->findOneBySlug($slug);
        if (!$produit) {
            return $this->redirectToRoute('produits');
        }

        return $this->render('produits/show.html.twig', [
            'produit' => $produit
        ]);
    }
}
