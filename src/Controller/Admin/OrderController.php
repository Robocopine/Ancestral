<?php

namespace App\Controller\Admin;

use App\Entity\Order;
use App\Entity\Carrier;
use App\Form\Admin\OrderType;
use App\Service\PaginationService;
use App\Repository\OrderRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/admin/commande', name: 'admin_order_')]
class OrderController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager){
        $this->entityManager = $entityManager;
    }
    
    #[Route('s/{page<\d+>?1}', name: 'index', methods: ['GET'])]
    public function index(PaginationService $pagination, $page): Response
    {
        $pagination->setEntityClass(Order::class)
                ->setLimit(15)
                ->setPage($page);
        ;

        return $this->render('admin/order/index.html.twig', [
            'controller_name' => 'Commandes',
            'pagination' => $pagination,
        ]);
    }

    #[Route('/créer', name: 'new', methods: ['GET', 'POST'])]
    public function new(Request $request, OrderRepository $orderRepository): Response
    {
        $order = new Order();
        $form = $this->createForm(OrderType::class, $order);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $orderRepository->save($order, true);

            return $this->redirectToRoute('admin_order_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/order/new.html.twig', [
            'order' => $order,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(Order $order): Response
    {
        return $this->render('admin/order/show.html.twig', [
            'order' => $order,
            'controller_name' => 'Commande '. $order->getReference(),
        ]);
    }

    #[Route('/{id}/modifier', name: 'edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Order $order, OrderRepository $orderRepository): Response
    {
        $carrier = $this->entityManager->getRepository(Carrier::class)->findOneByName($order->getCarrierName());
        $form = $this->createForm(OrderType::class, $order);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $carrier = $form['carrier']->getData();
            $delivery = $form['delivery']->getData();
            dd($delivery);
            $order->setCarrierName($carrier->getName());
            $order->setCarrierPrice($carrier->getPrice());
            $orderRepository->save($order, true);

            return $this->redirectToRoute('admin_order_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/order/edit.html.twig', [
            'order' => $order,
            'form' => $form,
            'controller_name' => 'Modifier commande n°'.$order->getId(),
        ]);
    }

    #[Route('/{id}', name: 'delete', methods: ['POST'])]
    public function delete(Request $request, Order $order, OrderRepository $orderRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$order->getId(), $request->request->get('_token'))) {
            $orderRepository->remove($order, true);
        }

        return $this->redirectToRoute('admin_order_index', [], Response::HTTP_SEE_OTHER);
    }
}
