<?php

namespace App\Controller;

use App\Entity\Order;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class MembreOrderController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/membre/mes-commandes', name: 'membre_order')]
    public function index(): Response
    {
        $orders = $this->entityManager->getRepository(Order::class)->findSuccessOrders($this->getUser());

        return $this->render('membre/order.html.twig', [
            'orders' => $orders
        ]);
    }
    
    #[Route('/membre/mes-commandes/{reference}', name: 'membre_order_show')]
    public function show($reference)
    {
        $order = $this->entityManager->getRepository(Order::class)->findOneByReference($reference);

        if (!$order || $order->getMembre() != $this->getUser()) {
            return $this->redirectToRoute('membre_order');
        }

        return $this->render('membre/order_show.html.twig', [
            'order' => $order
        ]);
    }
}
