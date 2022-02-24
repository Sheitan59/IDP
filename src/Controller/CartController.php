<?php

namespace App\Controller;

use App\Classe\Cart;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CartController extends AbstractController
{

    
    #[Route('/mon-panier', name: 'cart')]
    public function index(Cart $cart): Response
    {

        return $this->render('cart/index.html.twig', [
            'controller_name' => 'CartController',
            'cart' => $cart->getFull()
        ]);
    }

    #[Route('/mon-panier/add/{id}', name: 'add_to_cart')]
    public function add(Cart $cart ,$id)
    {
        $cart->add($id);
        return $this->redirectToRoute('cart');
    }

    #[Route('/mon-panier/remove', name: 'remove_my_cart')]
    public function remove(Cart $cart)
    {
        $cart->remove();
        return $this->redirectToRoute('produits');
    }

    #[Route('/mon-panier/delete/{id}', name: 'delete_to_cart')]
    public function delete(Cart $cart,$id)
    {
        $cart->delete($id);
        return $this->redirectToRoute('cart');
    }

    
    #[Route('/mon-panier/decrease/{id}', name: 'decrease_to_cart')]
    public function decrease(Cart $cart, $id)
    {
        $cart->decrease($id);

        return $this->redirectToRoute('cart');
    }
}
