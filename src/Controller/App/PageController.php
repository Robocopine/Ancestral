<?php

namespace App\Controller\App;

use App\Service\CartService;
use App\Service\LoginService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class PageController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(CartService $sessionCart): Response
    {
        return $this->render('app/page/index.html.twig', [
            'controller_name' => 'Accueil',
            'sessionCart' => $sessionCart,
        ]);
    }

}
