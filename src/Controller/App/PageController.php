<?php

namespace App\Controller\App;

use App\Service\LoginService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class PageController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(AuthenticationUtils $authenticationUtils): Response
    {
        return $this->render('app/page/index.html.twig', [
            'controller_name' => 'Accueil',
        ]);
    }

}
