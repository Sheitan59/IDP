<?php

namespace App\Controller;

use App\Classe\Mail;
use App\Entity\Utilisateur;
use App\Form\EnregistrementType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class EnregistrementController extends AbstractController
{
    private $entityManager;
    public function __construct(EntityManagerInterface $entityManager)
    {
     $this->entityManager = $entityManager;   
    }

    #[Route('/inscription', name: 'enregistrement')]
    public function index(Request $request, UserPasswordHasherInterface $hasher): Response
    {
        $util = new Utilisateur();
        $notification = null;

        
        $form = $this->createForm(EnregistrementType::class , $util);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid() ){
            $util = $form->getData();

            $search_email = $this->entityManager->getRepository(Utilisateur::class)->findOneByEmail($util->getEmail());
            if (!$search_email) {
                 $password = $hasher->hashPassword($util,$util->getPassword());
                 $util->setPassword($password);
                 $notification = "Votre inscription s'est correctement déroulée. Vous pouvez dès à présent vous connecter à votre compte.";
                 $this->entityManager->persist($util);
                 $this->entityManager->flush();
                 $mail = new Mail();
                 $content = "Bonjour ".$util->getNomPrenom()."<br/>Bienvenue sur Indoor Proxy Garden ";
                 $mail->send($util->getEmail(), $util->getNomPrenom(), 'Bienvenue sur Indoor Proxy Garden', $content);
                 return $this->redirectToRoute('app_login');
                } else {
                    $notification = "L'email que vous avez renseigné existe déjà.";
                }
        }
        return $this->render('enregistrement/index.html.twig', [
            'controller_name' => 'EnregistrementController',
            'notification' => $notification,
            'form' => $form->createView()
        ]);
    }
}
