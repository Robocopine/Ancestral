<?php

namespace App\Controller\App;

use App\Classe\Search;
use App\Entity\Product;
use App\Entity\Category;
use App\Form\App\SearchType;
use App\Service\CartService;
use App\Service\PaginationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/produit', name: 'product')]
class ProductController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('s/{page<\d+>?1}', name: '_index')]
    public function index(Request $request, PaginationService $pagination, CartService $sessionCart, $page): Response
    {
        $products = $this->entityManager->getRepository(Product::class)->findAll();

        $search = new Search();
        $form = $this->createForm(SearchType::class, $search);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            
            $products = $this->entityManager->getRepository(Product::class)->findWithSearch($search);
            
            
            $pagination->setRequestEntityComplete($products)
                ->setLimit(15)
                ->setPage($page)
            ;
            
                      
        }else{
            $pagination->setEntityClass(Product::class)
                ->setLimit(15)
                ->setPage($page)
            ;

        }

        return $this->render('app/product/index.html.twig', [
            'controller_name' => 'Nos produits',
            'pagination' => $pagination,
            'form' => $form,
            'sessionCart' => $sessionCart,
        ]);
    }
}
