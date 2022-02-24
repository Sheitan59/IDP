<?php

namespace App\Controller;

use App\Classe\Cart;
use App\Entity\Adresse;
use App\Form\AdresseType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class MembreAdresseController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }
    
    #[Route('/membre/adresse', name: 'membre_adresse')]
    public function index(): Response
    {
        return $this->render('membre/adresse.html.twig');
    }

    #[Route('/membre/ajouter-une-adresse', name: 'membre_adresse_add')]
    public function add(Cart $cart, Request $request)
    {
        $adresse = new Adresse();

        $form = $this->createForm(AdresseType::class, $adresse);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $adresse->setMembre($this->getUser());
            $this->entityManager->persist($adresse);
            $this->entityManager->flush();
            if ($cart->get()) {
                return $this->redirectToRoute('order');
            } else {
                 return $this->redirectToRoute('membre_adresse');
             }
            return $this->redirectToRoute('membre_adresse');
        }

        return $this->render('membre/adresse_form.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/membre/modifier-une-adresse/{id}', name: 'membre_adresse_edit')]
    public function edit(Request $request, $id)
    {
        $adresse = $this->entityManager->getRepository(Adresse::class)->findOneById($id);

        if (!$adresse || $adresse->getMembre() != $this->getUser()) {
            return $this->redirectToRoute('membre_adresse');
        }

        $form = $this->createForm(AdresseType::class, $adresse);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();
            return $this->redirectToRoute('membre_adresse');
        }

        return $this->render('membre/adresse_form.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/membre/supprimer-une-adresse/{id}', name: 'membre_adresse_delete')]
    public function delete($id)
    {
        $adresse = $this->entityManager->getRepository(Adresse::class)->findOneById($id);

        if ($adresse && $adresse->getMembre() == $this->getUser()) {
            $this->entityManager->remove($adresse);
            $this->entityManager->flush();
        }

        return $this->redirectToRoute('membre_adresse');
    }

}

