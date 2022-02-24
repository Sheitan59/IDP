<?php

namespace App\Controller;

use App\Classe\Cart;
use App\Classe\Mail;
use App\Entity\Order;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class OrderValidateController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }


    #[Route('/commande/merci/', name: 'order_validate')]
    public function index(Cart $cart): Response
    {
        // Stripe::setApiKey('sk_test_51KO1u2IR2TsPfGurZjfTFudLN0w4PvrkiNAwc2briXUjx8Zny5mJQfYjRB0b6qzt7IZjDoHMl50Jp52QwwFeEVr600lvsya53h');
        // $stripeSessionId = Session::retrieve($request->get('session_id'))->id;
        $stripeSessionId = $_GET['session_id'];
        $order = $this->entityManager->getRepository(Order::class)->findOneByStripeSessionId($stripeSessionId);
        // dd(Session::retrieve($request->get('session_id'))->id);
        // dd($stripeSessionId->id);
        if (!$order || $order->getMembre() != $this->getUser()) {
            return $this->redirectToRoute('home');
        }
        

        if ($order->getState() == 0) {
            // Vider la session "cart"
            $cart->remove();

            // Modifier le statut isPaid de notre commande en mettant 1
            $order->setState(1);
            $this->entityManager->flush();
                        // Envoyer un email à notre client pour lui confirmer sa commande
                        $mail = new Mail();
                        $content = "Bonjour ".$order->getMembre()->getNomPrenom()."<br/>Merci pour votre commande.<br>";
                        $mail->send($order->getMembre()->getEmail(), $order->getMembre()->getNomPrenom(), 'Votre commande'. $order->getReference(). ' est bien validée.<br/> Nous vous tiendrons informé des la preparation et de la livraison de celle-ci', $content);

        }
        return $this->render('order_validate/index.html.twig', [
            'order' => $order
        ]);
    }
}
