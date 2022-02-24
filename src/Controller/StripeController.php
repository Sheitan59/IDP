<?php

namespace App\Controller;

use Stripe\Stripe;
use App\Classe\Cart;
use App\Entity\Order;
use App\Entity\Produits;
use Stripe\Checkout\Session;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class StripeController extends AbstractController
{
    #[Route('/commande/create-session/{reference}', name: 'stripe_create_session')]
    public function index(EntityManagerInterface $entityManager, Cart $cart, $reference): Response
    {
        $product_for_stripe = [];
        $YOUR_DOMAIN = 'https://indoorproxygarden.com';

        $order = $entityManager->getRepository(Order::class)->findOneByReference($reference);
        if(!$order){
          return $this->redirectToRoute('order');
        }

        foreach ($order->getOrderDetails()->getValues() as $product ) {
          $product_object = $entityManager->getRepository(Produits::class)->findOneByNom($product->getProduct());
          $product_for_stripe[] = [
            'price_data' => [
                'currency' => 'eur',
                'unit_amount' => $product->getPrice(),
                'product_data' => [
                    'name' => $product->getProduct(),
                    'images' => [$YOUR_DOMAIN."/uploads".$product_object->getImage()]
                  ],
            ],
            'quantity' => $product->getQuantity(),
          ];
        }
        
        $product_for_stripe[] = [
          'price_data' => [
              'currency' => 'eur',
              'unit_amount' => $order->getCarrierPrix(),
              'product_data' => [
                  'name' => $order->getCarrierNom(),
                  'images' => [$YOUR_DOMAIN],
              ],
          ],
          'quantity' => 1,
      ];

        Stripe::setApiKey('clé stripe');
        $checkout_session = Session::create([
            'customer_email' => $this->getUser()->getEmail(),
            'line_items' => [
                $product_for_stripe
            ],
            'mode' => 'payment',
            //  'success_url' => $YOUR_DOMAIN . '/success',
            //  'cancel_url' => $YOUR_DOMAIN . '/cancel',
             'success_url' => $YOUR_DOMAIN . '/commande/merci/?session_id={CHECKOUT_SESSION_ID}',
             'cancel_url' => $YOUR_DOMAIN . '/commande/erreur/?session_id={CHECKOUT_SESSION_ID}',
          ]);
          // dd($checkout_session->id);
          $order->setStripeSessionId($checkout_session->id);
          $entityManager->flush();
          // dd($checkout_session);
          // $response = new JsonResponse(['id' => $checkout_session->id]);
          // return $response;
          return $this->redirect($checkout_session->url);
    }
}
