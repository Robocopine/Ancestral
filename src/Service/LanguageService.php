<?php

namespace App\Service;

use Twig\Environment;
use App\Classe\LegalAge;
use Symfony\Component\HttpFoundation\RequestStack;

class LanguageService {

  private $templatePath;
  private $language;

  public function __construct(RequestStack $request, LegalAge $legalAge, $templatePath, Environment $twig)
  {
    $this->language = $request ->getCurrentRequest()->attributes->get('_locale');
    $this->legalAge = $legalAge;
    $this->twig = $twig;
    $this->templatePath = $templatePath;
  }

  public function getLanguage() {
    if($this->language == null){ $this->language = "fr"; }
    return $this->language;
  }

  public function setLanguage($language) {
    $this->language = $language;

    return $this;
  }

  // Gets pagination.html.twig
  public function getTemplatePath(){
    return $this->templatePath;
  }

  public function setTemplatePath($templatePath)
  {
    $this->templatePath = $templatePath;

    return $this;
  }

  // Sends Data to template
  public function display(){
    $this->twig->display($this->templatePath, [
        'lg' => $this->language,
        'legalAge' => $this->legalAge,
    ]);
  }

}