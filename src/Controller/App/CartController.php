<?php

namespace App\Controller\App;

use App\Classe\Cart;
use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/mon-panier', name: 'cart')]
class CartController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }
    #[Route('', name: '_index')]
    public function index(Cart $cart): Response
    {
        return $this->render('app/cart/index.html.twig', [
            'controller_name' => 'CartController',
            'cart' => $cart->getFull(),
        ]);
    }

    #[Route('/ajouter/{id}/{route}', name: '_add')]
    public function add(Cart $cart, Request $request, $id, $route)
    {
       $cart->add($id);

       return $this->redirectToroute($route);
    }

    #[Route('/diminuer/{id}/{route}', name: '_decrease')]
    public function descrease(Cart $cart, Request $request, $id, $route)
    {
        $cart->decrease($id);

        return $this->redirectToroute($route);
    }

    #[Route('/enlever/{id}', name: '_remove')]
    public function remove(Cart $cart)
    {
        $cart->remove();
        return $this->redirectToroute('cart_index');

    }

    #[Route('/supprimer/{id}', name: '_delete')]
    public function delete(Cart $cart, $id)
    {
        $cart->delete($id);
        return $this->redirectToroute('cart_index');

    }
}
