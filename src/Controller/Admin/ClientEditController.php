<?php

namespace App\Controller\Admin;

use App\Entity\ClientEdit;
use App\Form\Admin\ClientEditType;
use App\Service\PaginationService;
use App\Service\FileUploaderService;
use App\Repository\SectionRepository;
use App\Repository\ClientEditRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/admin/client/élément', name: 'admin_client_edit_')]
class ClientEditController extends AbstractController
{
    #[Route('s/{page<\d+>?1}', name: 'index', methods: ['GET'])]
    public function index(PaginationService $pagination, ClientEditRepository $clientEditRepository, $page): Response
    {
        $pagination->setEntityClass(ClientEdit::class)
                ->setLimit(15)
                ->setPage($page);
        ;

        return $this->render('admin/client_edit/index.html.twig', [
            'controller_name' => 'Éléments à personnaliser',
            'pagination' => $pagination,
        ]);
    }

    /**
     * Create a element for a section (only for Webmaster)
     */
    #[IsGranted('ROLE_WEBMASTER')]
    #[Route('/nouveau/{section?}', name: 'new', methods: ['GET', 'POST'])]
    public function new(Request $request, FileUploaderService $fileUploader, ClientEditRepository $clientEditRepository, SectionRepository $sectionRepository, $section): Response
    {
        // Get section name by url data and set section by name
        if($section){
            $sectionSelected = $sectionRepository->findOneByName($section);
        }else{
            $sectionSelected = null;
        }
        // Get and Treat form 
        $clientEdit = new ClientEdit();
        $form = $this->createForm(ClientEditType::class, $clientEdit, [
            'sectionSelected' => $sectionSelected,
        ]);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if($form->get('file')->getData() != null){
                // Upload file if section type is file and set content = fileName (string)
                $contentFile = $form['file']->getData();
                $contentFileName = $fileUploader->setTargetDirectory('src/edit/img')->upload($contentFile);
                $content = $contentFileName;
            }else{
                // set content = string if section type is string
                $content = $form['string']->getData();
            }
            $clientEdit->setContent($content);
            $clientEdit->setSection($form['section']->getData());
            $clientEditRepository->save($clientEdit, true);

            return $this->redirectToRoute('admin_client_edit_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/client_edit/new.html.twig', [
            'controller_name' => 'Nouvel élément à personnaliser',
            'form' => $form,
            'section' => $sectionSelected
        ]);
    }

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(ClientEdit $clientEdit): Response
    {
        return $this->render('admin/client_edit/show.html.twig', [
            'client_edit' => $clientEdit,
            
        ]);
    }

    #[Route('/{id}/modifier', name: 'edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, FileUploaderService $fileUploader, ClientEdit $clientEdit, ClientEditRepository $clientEditRepository): Response
    {

        // Get and Treat form 
        $form = $this->createForm(ClientEditType::class, $clientEdit);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if($form->get('file')->getData() != null){
                // Upload file if section type is file and set content = fileName (string)
                $contentFile = $form['file']->getData();
                $contentFileName = $fileUploader->setTargetDirectory('src/edit/img')->upload($contentFile);
                $content = $contentFileName;
            }else{
                // set content = string if section type is string
                $content = $form['string']->getData();
            }
            $clientEdit->setContent($content);
            $clientEdit->setSection($clientEdit->getSection());

            $clientEditRepository->save($clientEdit, true);

            return $this->redirectToRoute('admin_client_edit_index', [], Response::HTTP_SEE_OTHER); 
            
        }

        return $this->renderForm('admin/client_edit/edit.html.twig', [
            'controller_name' => 'Modifier'. $clientEdit->getSection()->getName(),
            'client_edit' => $clientEdit,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'delete', methods: ['POST'])]
    public function delete(Request $request, ClientEdit $clientEdit, ClientEditRepository $clientEditRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$clientEdit->getId(), $request->request->get('_token'))) {
            $clientEditRepository->remove($clientEdit, true);
        }

        return $this->redirectToRoute('admin_client_edit_index', [], Response::HTTP_SEE_OTHER);
    }
}
