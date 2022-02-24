<?php

namespace App\Controller;

use App\Entity\Order;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class OrderCancelController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }
    #[Route('/commande/erreur/', name: 'order_cancel')]
    public function index(): Response
    {
        $stripeSessionId = $_GET['session_id'];
        $order = $this->entityManager->getRepository(Order::class)->findOneByStripeSessionId($stripeSessionId);

        if (!$order || $order->getMembre() != $this->getUser()) {
            return $this->redirectToRoute('home');
        }
        
        return $this->render('order_cancel/index.html.twig', [
            'order' => $order
        ]);
    }
}
