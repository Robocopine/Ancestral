<?php

namespace App\Service;

use App\Classe\Cart;
use Twig\Environment;

class CartService
{
    private $cart;
    private $templatePath;
    private $twig;

    public function __construct(Cart $cart, $templatePath, Environment $twig)
    {
        $this->cart = $cart;
        $this->twig = $twig;
        $this->templatePath = $templatePath;
    }

    public function getTemplatePath(){
        return $this->templatePath;
    }

    public function setTemplatePath($templatePath)
    {
        $this->templatePath = $templatePath;

        return $this;
    }
    public function display() {

        $this->twig->display($this->templatePath, [
            'cartFull' => $this->cart->getFull(),
        ]);
  
    }
}