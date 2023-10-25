<?php

namespace App\Controller\Account;

use App\Entity\Order;
use App\Classe\Search;
use App\Entity\Carrier;
use App\Entity\Product;
use App\Form\App\SearchType;
use App\Service\CartService;
use App\Service\EditService;
use App\Service\PaginationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/compte/commande', name: 'account_order_')]
class OrderController extends AbstractController
{
    private $entityManager;
    private $editService;

    public function __construct(EntityManagerInterface $entityManager, EditService $editService)
    {
        $this->entityManager = $entityManager;
        $this->editService = $editService;
    }
    
    #[Route('s/{page<\d+>?1}/', name: 'index')]
    public function index(Request $request, CartService $sessionCart, PaginationService $pagination, $page): Response
    {
        $orders = $this->entityManager->getRepository(Order::class)->findSuccessOrders($this->getUser());
       
        $pagination->setRequestEntityComplete($orders)
            ->setLimit(15)
            ->setPage($page)
        ;
        
        // Filter by name and category
        $search = new Search();
        $formSearch = $this->createForm(SearchType::class, $search);
        $formSearch->handleRequest($request);
        
        return $this->render('account/order/index.html.twig', [
            'controller_name' => 'Mes commandes',
            'formSearch' => $formSearch,
            'sessionCart' => $sessionCart,
            'orders' => $pagination->getData(),
            'items' => $this->editService->getItems(),
        ]);
    }

    #[Route('/{reference}', name: 'show')]
    public function show(Request $request, CartService $sessionCart, $reference): Response
    {
        $order = $this->entityManager->getRepository(Order::class)->findOneByReference($reference);

        if(!$order || $order->getUser() != $this->getUser()){
            return $this->redirectToRoute('account_order_index');
        }

        $carrier = $this->entityManager->getRepository(Carrier::class)->findOneByName($order->getCarrierName());
        foreach($order->getOrderDetails()->getValues() as $product){
            $product_object[] = ['productDetails' => $this->entityManager->getRepository(Product::class)->findOneByName($product->getProduct()) , 'quantity' => $product->getQuantity()];
        }

        // Filter by name and category
        $search = new Search();
        $formSearch = $this->createForm(SearchType::class, $search);
        $formSearch->handleRequest($request);
        
        return $this->render('account/order/show.html.twig', [
            'controller_name' => 'Ma commande ' . $order->getReference() ,
            'formSearch' => $formSearch,
            'sessionCart' => $sessionCart,
            'order' => $order,
            'carrier' => $carrier,
            'cartFull' => $product_object,
            'items' => $this->editService->getItems(),
        ]);
    }
}
