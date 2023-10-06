<?php

namespace App\Controller\App;

use App\Classe\Cart;
use App\Classe\Mail;
use App\Classe\Search;
use App\Entity\Product;
use App\Form\App\SearchType;
use App\Service\CartService;
use App\Service\LoginService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class PageController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }
    
    #[Route('/', name: 'home')]
    public function index(CartService $sessionCart, Cart $cart, Request $request): Response
    {
        $limitedProducts = $this->entityManager->getRepository(Product::class)->findBy(
            ['isLimited' => '1'],
        );

        $lastProducts = $this->entityManager->getRepository(Product::class)->findLastProducts();
        // Filter by name and category
        $search = new Search();
        $formSearch = $this->createForm(SearchType::class, $search);
        $formSearch->handleRequest($request);

        return $this->render('app/page/index.html.twig', [
            'controller_home' => 'controller_home',
            'cart' => $cart->getFull(),
            'sessionCart' => $sessionCart,
            'formSearch' => $formSearch,
            'limitedProducts' => $limitedProducts,
            'lastProducts' => $lastProducts,
        ]);
    }

}
