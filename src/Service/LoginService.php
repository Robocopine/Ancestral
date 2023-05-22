<?php

namespace App\Service;

use Twig\Environment;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class LoginService {


    public function __construct(RequestStack $request, AuthenticationUtils $authenticationUtils, $templatePath, Environment $twig){
        $this->errorLogin = $authenticationUtils->getLastAuthenticationError();
        $this->lastUsername = $authenticationUtils->getLastUsername();
        $this->templatePath = $templatePath;
        $this->twig = $twig;
        
    }

    public function getErrorLogin(){
        return $this->errorLogin;
    }
    
    public function setErrorLogin($errorLogin)
    {
        $this->errorLogin = $errorLogin;
    
        return $this;
    }

    public function getLastUsername(){
        
        return $this->lastUsername;
    }
    
    public function setLastUsername($lastUsername)
    {
        $this->lastUsername = $lastUsername;
    
        return $this;
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
            'last_username' => $this->lastUsername,
            'errorLogin' => $this->errorLogin,
        ]);
    
      }
}