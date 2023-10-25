<?php

namespace App\Controller\App;

use DateTime;
use App\Classe\Cart;
use App\Entity\Order;
use DateTimeImmutable;
use App\Entity\Address;
use App\Entity\Carrier;
use App\Entity\Product;
use Stripe\StripeClient;
use App\Form\App\OrderType;
use App\Entity\OrderDetails;
use App\Service\EditService;
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
    private $editService;

    public function __construct(EntityManagerInterface $entityManager, EditService $editService)
    {
        $this->entityManager = $entityManager;
        $this->editService = $editService;
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
            'items' => $this->editService->getItems(),
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
            $delivery_content .= '<br/>'.$delivery->getPhone();
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
                'items' => $this->editService->getItems(),
            ]);

            return $this->redirectToroute('cart_index');
        } 
    }

    #[Route('/merci/{stripeSessionId}', name: 'validate')]
    public function validate($stripeSessionId, Cart $cart)
    {
        $order = $this->entityManager->getRepository(Order::class)->findOneByStripeSessionId($stripeSessionId);
        if(!$order || ($order->getUser() != $this->getUser())) {
            return $this->redirectToRoute('home');
        }

        if(!$order->isIsPaid()) {
            $cart->remove();
            $order->setIsPaid(1);
            $this->entityManager->flush();
            // send success mail
        }

        $carrier = $this->entityManager->getRepository(Carrier::class)->findOneByName($order->getCarrierName());
        foreach($order->getOrderDetails()->getValues() as $product){
            $product_object[] = ['productDetails' => $this->entityManager->getRepository(Product::class)->findOneByName($product->getProduct()) , 'quantity' => $product->getQuantity()];
        }
        return $this->render('app/order/success.html.twig', [
            'controller_name' => 'Confirmation de commande',
            'order' => $order,
            'cartFull' => $product_object,
            'carrier' => $carrier,
            'delivery' => $order->getDelivery(),
            'reference' => $order->getReference(),
            'items' => $this->editService->getItems(),
        ]);
    }

    #[Route('/erreur/{stripeSessionId}', name: 'cancel')]
    public function cancel($stripeSessionId)
    {
        $order = $this->entityManager->getRepository(Order::class)->findOneByStripeSessionId($stripeSessionId);
        
        if(!$order || ($order->getUser() != $this->getUser())) {
            return $this->redirectToRoute('home');
        }

        $carrier = $this->entityManager->getRepository(Carrier::class)->findOneByName($order->getCarrierName());
        foreach($order->getOrderDetails()->getValues() as $product){
            $product_object[] = ['productDetails' => $this->entityManager->getRepository(Product::class)->findOneByName($product->getProduct()) , 'quantity' => $product->getQuantity()];
        }

        return $this->render('app/order/cancel.html.twig', [
            'controller_name' => 'Confirmation de commande',
            'order' => $order,
            'cartFull' => $product_object,
            'carrier' => $carrier,
            'delivery' => $order->getDelivery(),
            'reference' => $order->getReference(),
            'items' => $this->editService->getItems(),
        ]);
    }
}
