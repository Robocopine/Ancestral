<?php

namespace App\Controller\App;

use Stripe\Stripe;
use App\Classe\Cart;
use Stripe\Checkout\Session;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class StripeController extends AbstractController
{
    #[Route('/commande/create-session', name: 'stripe_create_session')]
    public function index(Cart $cart)
    {
        $products_for_stripe = [];

        foreach($cart->getFull() as $product){
            $products_for_stripe[] = [
                'price_data' => [
                  'currency' => $_ENV['STRIPE_CURRENCY'],
                  'product_data' => [
                    'name' => $product['product']->getName(),
                    'images' =>  [$_ENV['YOUR_DOMAIN'].$product['product']->getIllustration()],
                  ],
                  'unit_amount' => $product['product']->getPrice()*100,
                ],
                'quantity' => $product['quantity'],
            ];
        }

        Stripe::setApiKey($_ENV['STRIPE_SECRETKEY']);

        $checkout_session = Session::create([
            'line_items' => [
                $products_for_stripe
            ],
            'mode' => 'payment',
            'success_url' => $_ENV['YOUR_DOMAIN'] . '/success',
            'cancel_url' => $_ENV['YOUR_DOMAIN'] . '/cancel',
        ]);

        return new JsonResponse(['id' => $checkout_session->id]);
    }
}
