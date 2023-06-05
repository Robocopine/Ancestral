<?php

namespace App\Controller\App;

use App\Classe\Cart;
use App\Form\App\OrderType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class OrderController extends AbstractController
{
    #[Route('/commande', name: 'order')]
    public function index(Cart $cart): Response
    {
        $form = $this->createForm(OrderType::class, null, [
            'user' => $this->getUser(),
        ]);

        return $this->render('app/order/index.html.twig', [
            'form' => $form->createView(),
            'controller_name' => 'OrderController',
            'cartFull' => $cart->getFull(),
        ]);
    }
}
