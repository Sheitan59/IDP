<?php

namespace App\Controller;

use App\Classe\Cart;
use App\Entity\Order;
use App\Form\OrderType;
use App\Entity\OrderDetail;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class OrderController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/commande', name: 'order')]
    public function index(Cart $cart, Request $request): Response
    {
        if (!$this->getUser()->getAdresses()->getValues())
        {
            return $this->redirectToRoute('membre_adresse_add');
        }

        $form = $this->createForm(OrderType::class, null , [
            'user' => $this->getUser()
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            dd($form->getData());

        }
        return $this->render('order/index.html.twig',[
            'form' => $form->createView(),
            'cart' => $cart->getFull()
        ]
        );
    }


    /**
     * @Route("/commande/recapitulatif", name="order_recap", methods={"POST"})
     */
    public function add(Cart $cart, Request $request): Response
    {
        $form = $this->createForm(OrderType::class, null , [
            'user' => $this->getUser()
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            
            $date = new \DateTimeImmutable();
            $carriers = $form->get('carriers')->getData();
            $delivery = $form->get('adresses')->getData();
            $delivery_content = $delivery->getNom().' '.$delivery->getPrenom();
            $delivery_content .= '<br>'.$delivery->getTelephone();
            if ($delivery->getEntreprise()) {
                $delivery_content .= '<br>'.$delivery->getEntreprise();
            }
            $delivery_content .= '<br>'.$delivery->getAdresse();
            $delivery_content .= '<br>'.$delivery->getCodePostal().' '.$delivery->getVille();
            $delivery_content .= '<br>'.$delivery->getPays();


            $order = new Order();
            $reference = $date->format('dmY').'-'.uniqid();
            $order->setReference($reference);
            $order->setMembre($this->getUser());
            $order->setCreatedAt($date);
            $order->setCarrierNom($carriers->getNom());
            $order->setCarrierPrix($carriers->getPrix()*100);
            $order->setDelivry($delivery_content);
            $order->setState(0);

            $this->entityManager->persist($order);


            foreach ($cart->getFull() as $product) {
                $orderDetails = new OrderDetail();
                $orderDetails->setMyOrder($order);
                $orderDetails->setProduct(($product['produit']->getNom()));
                $orderDetails->setQuantity($product['quantity']);
                $orderDetails->setPrice(($product['produit']->getPrice()));
                $orderDetails->setTotal(($product['produit']->getPrice() * $product['quantity']));

                $this->entityManager->persist($orderDetails);

                // $product_for_stripe[] = [
                //     'price_data' => [
                //         'currency' => 'eur',
                //         'unit_amount' => $product['produit']->getPrice(),
                //         'product_data' => [
                //             'name' => $product['produit']->getNom(),
                //         ],
                //     ],
                //   'quantity' => $product['quantity'],
                // ];
            }

            $this->entityManager->flush();

            // $YOUR_DOMAIN = 'https://127.0.0.1:8000';

                
    
            // Stripe::setApiKey('sk_test_51KO1u2IR2TsPfGurZjfTFudLN0w4PvrkiNAwc2briXUjx8Zny5mJQfYjRB0b6qzt7IZjDoHMl50Jp52QwwFeEVr600lvsya53h');
            // $checkout_session = Session::create([
                
            //     'line_items' => [
            //         $product_for_stripe
            //     ],
            //     'mode' => 'payment',
            //     'success_url' => $YOUR_DOMAIN . '/success',
            //     'cancel_url' => $YOUR_DOMAIN . '/cancel',
            //     //   'success_url' => $YOUR_DOMAIN . '/commande/merci/{CHECK_OUT_SESSION_ID}',
            //     //   'cancel_url' => $YOUR_DOMAIN . '/commande/erreur/{CHECK_OUT_SESSION_ID}',
            //   ]);
            //   dump($checkout_session->id);
            //   dd($checkout_session);
            //   $order->setStripeSessionId($checkout_session->id);
           

            return $this->render('order/add.html.twig',[
                'cart' => $cart->getFull(),
                'carrier' => $carriers,
                'delivery' => $delivery_content,
                // 'stripe_checkout_session' => $checkout_session->id
                'reference' => $order->getReference()
            ]
            );
        
            return $this->redirectToRoute('cart');
        }
    }
}
