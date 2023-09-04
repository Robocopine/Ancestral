<?php

namespace App\Controller\App;

use DateTime;
use App\Classe\Cart;
use App\Entity\Order;
use DateTimeImmutable;
use App\Entity\Address;
use Stripe\StripeClient;
use App\Form\App\OrderType;
use App\Entity\OrderDetails;
use App\Form\Security\AddressType;
use App\Repository\AddressRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/commande', name: 'order_')]
class OrderController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager){
        $this->entityManager = $entityManager;
    }

    #[Route('', name: 'index')]
    public function index(Cart $cart, Request $request, AddressRepository $addressRepository): Response
    {
        $user = $this->getUser();
        $form = $this->createForm(OrderType::class, null, [
            'user' => $user,
        ]);

        $address = new Address();
        $formAddress =  $this->createForm(AddressType::class, $address);
        $formAddress->handleRequest($request);

        if($formAddress->isSubmitted() && $formAddress->isValid()){
            $address->setUser($user);
            $addressRepository->save($address, true);

            return $this->redirectToRoute('order_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('app/order/index.html.twig', [
            'form' => $form->createView(),
            'controller_name' => 'OrderController',
            'cartFull' => $cart->getFull(),
            'formAddress' => $formAddress,
        ]);
    }

    #[Route('/récapitulatif', name: 'resume', methods: ['POST'])]
    public function add(Cart $cart, Request $request): Response
    {
    
        $form = $this->createForm(OrderType::class, null, [
            'user' => $this->getUser(),
        ]);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $date = new DateTimeImmutable('now');
            $carriers = $form->get('carriers')->getData();
            $delivery = $form->get('addresses')->getData();
            $delivery_content = $delivery->getFirstName().' '.$delivery->getLastName();
            $delivery_content = '<br/>'.$delivery->getPhone();
            if($delivery->getCompany()){
                $delivery_content .= '<br/>'.$delivery->getCompany();
            }

            $delivery_content .= '<br/>'.$delivery->getAddress();
            $delivery_content .= '<br/>'.$delivery->getPostal().' '.$delivery->getCity();
            $delivery_content .= '<br/>'.$delivery->getCountry();
            
            $order = new Order();
            $reference = $date->format('dmY').'-'.uniqid();
            $order->setReference($reference);
            $order->setUser($this->getUser());
            $order->setCreatedAt($date);
            $order->setCarrierName($carriers->getName());
            $order->setCarrierPrice($carriers->getPrice());
            $order->setDelivery($delivery_content);
            $order->setIsPaid(0);

            $this->entityManager->persist($order);

            // Save products cart in OrderDetails Entity
            foreach($cart->getFull() as $product){
                $orderDetails = new OrderDetails();
                $orderDetails->setMyOrder($order);
                $orderDetails->setProduct($product['product']->getName());
                $orderDetails->setQuantity($product['quantity']);
                $orderDetails->setPrice($product['product']->getPrice());
                $orderDetails->setTotal($product['product']->getPrice() * $product['quantity']);
                $this->entityManager->persist($orderDetails);
            }

            $this->entityManager->flush();

            return $this->render('app/order/add.html.twig', [
                'controller_name' => 'OrderController',
                'cartFull' => $cart->getFull(),
                'carrier' => $carriers,
                'delivery' => $delivery_content,
                'reference' => $order->getReference(),
            ]);

            return $this->redirectToroute('cart_index');
        } 
    }

    #[Route('/merci/{stripeSessionId}', name: 'validate')]
    public function validate($stripeSessionId)
    {
        $order = $this->entityManager->getRepository(Order::class)->findOneByStripeSessionId($stripeSessionId);
        dd($order);
        return $this->render('app/order/success.html.twig', [
            'controller_name' => 'Merci pour votre commande',
        ]);
    }
}
