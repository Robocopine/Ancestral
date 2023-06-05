<?php

namespace App\Controller\Admin;

use App\Entity\Carrier;
use App\Form\App\CarrierType;
use App\Service\PaginationService;
use App\Repository\CarrierRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/admin/carrier', name: 'admin_carrier_')]
class CarrierController extends AbstractController
{
    #[Route('s/{page<\d+>?1}', name: 'index', methods: ['GET'])]
    public function index(CarrierRepository $carrierRepository, PaginationService $pagination, $page): Response
    {
        $pagination->setEntityClass(Carrier::class)
                ->setLimit(15)
                ->setPage($page);
        ;

        return $this->render('admin/carrier/index.html.twig', [
            'controller_name' => 'Transporteurs',
            'pagination' => $pagination,
        ]);
    }

    #[Route('/new', name: 'new', methods: ['GET', 'POST'])]
    public function new(Request $request, CarrierRepository $carrierRepository): Response
    {
        $carrier = new Carrier();
        $form = $this->createForm(CarrierType::class, $carrier);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $carrierRepository->save($carrier, true);

            return $this->redirectToRoute('admin_carrier_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/carrier/new.html.twig', [
            'controller_name' => 'Nouveau transporteur',
            'carrier' => $carrier,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(Carrier $carrier): Response
    {
        return $this->render('admin/carrier/show.html.twig', [
            'carrier' => $carrier,
        ]);
    }

    #[Route('/{id}/edit', name: 'edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Carrier $carrier, CarrierRepository $carrierRepository): Response
    {
        $form = $this->createForm(CarrierType::class, $carrier);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $carrierRepository->save($carrier, true);

            return $this->redirectToRoute('admin_carrier_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/carrier/edit.html.twig', [
            'controller_name' => ' Modication du transporteurs : ' . $carrier->getName(),
            'carrier' => $carrier,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'delete', methods: ['POST'])]
    public function delete(Request $request, Carrier $carrier, CarrierRepository $carrierRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$carrier->getId(), $request->request->get('_token'))) {
            $carrierRepository->remove($carrier, true);
        }

        return $this->redirectToRoute('admin_carrier_index', [], Response::HTTP_SEE_OTHER);
    }
}
