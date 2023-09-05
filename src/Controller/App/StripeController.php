<?php

namespace App\Controller\App;

use Stripe\Stripe;
use App\Classe\Cart;
use App\Entity\Order;
use App\Entity\Product;
use Stripe\Checkout\Session;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class StripeController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager){
        $this->entityManager = $entityManager;
    }

    #[Route('/commande/create-session/{reference}', name: 'stripe_create_session')]
    public function index(Cart $cart, $reference)
    {
        $products_for_stripe = [];

        $order = $this->entityManager->getRepository(Order::class)->findOneByReference($reference);
        
        if(!$order){
            new JsonResponse(['error' => 'order']);
        }

        foreach($order->getOrderDetails()->getValues() as $product){
            $product_object = $this->entityManager->getRepository(Product::class)->findOneByName($product->getProduct());
            
            $products_for_stripe[] = [
                'price_data' => [
                  'currency' => $_ENV['STRIPE_CURRENCY'],
                  'product_data' => [
                    'name' => $product->getProduct(),
                    'images' =>  [$_ENV['YOUR_DOMAIN'].$product_object->getIllustration()],
                  ],
                  'unit_amount' => $product->getPrice()*100,
                ],
                'quantity' => $product->getQuantity(),
            ];
            
        }

        $products_for_stripe[] = [
            'price_data' => [
              'currency' => $_ENV['STRIPE_CURRENCY'],
              'product_data' => [
                'name' => $order->getCarrierName(),
                'images' =>  [$_ENV['YOUR_DOMAIN']],
              ],
              'unit_amount' => $order->getCarrierPrice()*100,
            ],
            'quantity' => 1,
        ];

        Stripe::setApiKey($_ENV['STRIPE_SECRETKEY']);

        $checkout_session = Session::create([
            'customer_email' => $this->getUser()->getEmail(),
            'line_items' => [
                $products_for_stripe
            ],
            'mode' => 'payment',
            'success_url' => $_ENV['YOUR_DOMAIN'] . '/commande/merci/{CHECKOUT_SESSION_ID}',
            'cancel_url' => $_ENV['YOUR_DOMAIN'] . '/commande/erreur/{CHECKOUT_SESSION_ID}',
        ]);

        $order->setStripeSessionId($checkout_session->id);
        $this->entityManager->flush();

        return new JsonResponse(['id' => $checkout_session->id]);
    }
}
