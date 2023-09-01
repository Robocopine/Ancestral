<?php

namespace App\Controller\App;

use App\Classe\Search;
use App\Form\App\SearchType;
use App\Service\CartService;
use App\Service\LoginService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class PageController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(CartService $sessionCart, Request $request): Response
    {
        // Filter by name and category
        $search = new Search();
        $formSearch = $this->createForm(SearchType::class, $search);
        $formSearch->handleRequest($request);

        return $this->render('app/page/index.html.twig', [
            'controller_name' => 'Accueil',
            'sessionCart' => $sessionCart,
            'formSearch' => $formSearch,
        ]);
    }

}
