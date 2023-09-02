<?php

namespace App\Controller\App;

use App\Classe\Search;
use App\Entity\Comment;
use App\Entity\Product;
use App\Entity\Category;
use App\Form\App\SearchType;
use App\Service\CartService;
use App\Form\App\CommentType;
use App\Repository\UserRepository;
use App\Service\PaginationService;
use App\Repository\CommentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/produit', name: 'product_')]
class ProductController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('s/{page<\d+>?1}/{search?null}', name: 'index')]
    public function index(Request $request, PaginationService $pagination, CartService $sessionCart, $page, $search): Response
    {
        $products = $this->entityManager->getRepository(Product::class)->findAll();

        // Filter by name and category
        $search = new Search();
        $formSearch = $this->createForm(SearchType::class, $search);
        $formSearch->handleRequest($request);

        // Display products by filters
        if($formSearch->isSubmitted() && $formSearch->isValid()){
            
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
            'formSearch' => $formSearch,
            'sessionCart' => $sessionCart,
            'search' => $search,
        ]);
    }

    #[Route('/{id}', name: 'show')]
    public function show(Product $product, Request $request, CommentRepository $commentRepository, UserRepository $users): Response
    {
        // Generate a form to comment
        $comment = new Comment();

        $formComment = $this->createForm(CommentType::class, $comment);
        $formComment->handleRequest($request);

        if($formComment->isSubmitted() && $formComment->isValid()){
            $commentRepository->save($comment, true);

            return $this->redirectToRoute('product_show', ['id'=> $product], Response::HTTP_SEE_OTHER);
        }

        return $this->render('app/product/show.html.twig', [
            'product' => $product,
            'formComment' => $formComment,
            'users' => $users->findAll(),
        ]);
    }
}
