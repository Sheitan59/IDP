<?php

namespace App\Controller;

use App\Form\ChangePasswordType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class MembrePasswordController extends AbstractController
{
    private $entityManager;
    public function __construct(EntityManagerInterface $entityManager)
    {
     $this->entityManager = $entityManager;   
    }
    #[Route('/compte/modifier-mon-mot-de-passe', name: 'membre_password')]
    public function index(Request $request, UserPasswordHasherInterface $hasher): Response
    {
        $notification = null;
        $util = $this->getUser();
        $form = $this->createForm(ChangePasswordType::class, $util);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid() ){
            $old_password = $form->get('old_password')->getData();

            if ($hasher->isPasswordValid($util,$old_password)) {
                $new_password = $form->get('new_password')->getData();
                $password = $hasher->hashPassword($util,$new_password);
                $util->setPassword($password);
                $this->entityManager->flush();
                $notification ="Votre mot de passe a bien été mis a jour";
            } else {
                $notification = "Votre mot de passe actuel est incorect";
            }

        }
        return $this->render('membre/password.html.twig', [
            'controller_name' => 'MembrePasswordController',
            'form' => $form->createView(),
            'notification' => $notification
        ]);
    }
}
