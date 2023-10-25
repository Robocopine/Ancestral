<?php

namespace App\Controller\Admin;

use App\Entity\Section;
use App\Form\Admin\SectionType;
use App\Service\PaginationService;
use App\Repository\SectionRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[IsGranted('ROLE_WEBMASTER')]
#[Route('/admin/section', name: 'admin_section_')]
class SectionController extends AbstractController
{
    #[Route('s/{page<\d+>?1}', name: 'index', methods: ['GET'])]
    public function index(PaginationService $pagination, SectionRepository $sectionRepository, $page): Response
    {
        $pagination->setEntityClass(Section::class)
                ->setLimit(15)
                ->setPage($page);
        ;

        return $this->render('admin/section/index.html.twig', [
            'controller_name' => 'Sections',
            'pagination' => $pagination,
        ]);
    }

    #[Route('/nouveau', name: 'new', methods: ['GET', 'POST'])]
    public function new(Request $request, SectionRepository $sectionRepository): Response
    {
        $section = new Section();
        $form = $this->createForm(SectionType::class, $section);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $sectionRepository->save($section, true);

            return $this->redirectToRoute('admin_section_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/section/new.html.twig', [
            'controller_name' => 'Nouvelle section',
            'section' => $section,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/modifier', name: 'edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Section $section, SectionRepository $sectionRepository): Response
    {
        $form = $this->createForm(SectionType::class, $section);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $sectionRepository->save($section, true);

            return $this->redirectToRoute('admin_section_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/section/edit.html.twig', [
            'controller_name' => 'Modifier la section' . $section->getName(),
            'section' => $section,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'delete', methods: ['POST'])]
    public function delete(Request $request, Section $section, SectionRepository $sectionRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$section->getId(), $request->request->get('_token'))) {
            $sectionRepository->remove($section, true);
        }

        return $this->redirectToRoute('admin_section_index', [], Response::HTTP_SEE_OTHER);
    }
}
