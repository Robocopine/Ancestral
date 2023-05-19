<?php

namespace App\Service;

use Twig\Environment;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class PaginationService {
  private $entityClass;
  private $limit = 10;
  private $currentPage = 1;
  private $manager;
  private $route;
  private $templatePath;
  private $twig;
  private $requestEntity;

  public function __construct(EntityManagerInterface $manager, RequestStack $request, $templatePath, Environment $twig)
  {
    $this->manager = $manager;
    $this->route = $request->getCurrentRequest()->attributes->get('_route');
    $this->currentPage = $request->getCurrentRequest()->attributes->get('page');
    $this->twig = $twig;
    $this->templatePath = $templatePath;
  }

  // Entity of pagination to be defined in Controller
  public function getEntityClass()
  {
    return $this->entityClass;
  }

  public function setEntityClass($entityClass)
  {
    $this->entityClass = $entityClass;

    return $this;
  }

  /*
   * get request to get repo with it
   */
  public function getRequestEntity(){
    return $this->requestEntity;
  }

  public function setRequestEntity($requestEntity){
    $this->requestEntity = $requestEntity;

    return $this;
  }

  public function getSpecialRepository()
  {
    return $this->manager->createQuery($this->requestEntity)->getResult();
  }


  // Limit results per page
  public function getLimit()
  {
    return $this->limit;
  }

  public function setLimit($limit)
  {
    $this->limit = $limit;

    return $this;
  }

  // Gets Current page in __construct
  public function getPage (): ?int
  {
    return $this->currentPage;
  }

  public function setPage($currentPage)
  {
    $this->currentPage = $currentPage;

    return $this;
  }

  // Nb of pages (Total \ Limit)
  public function getPages()
  {
    if(empty($this->entityClass)){
      if(empty($this->getSpecialRepository())){
        return null;
      }else{
        $total = count($this->getSpecialRepository());
      }
    }else{
      $repo = $this->manager->getRepository($this->entityClass);
      $total = count($repo->findAll());
    }

    $pages = ceil($total / $this->limit);

    return $pages;
  }

  // Nb of entity max
  public function getDataMax()
  {
    if(empty($this->entityClass)){
      if(empty($this->getSpecialRepository())){
        return null;
      }else{
        $dataMax = count($this->getSpecialRepository());
      }
    }else{
      $repo = $this->manager->getRepository($this->entityClass);
      $dataMax = count($repo->findAll());
    }

    return $dataMax;
  }

  // Id of first data in current page
  public function getDataMinPage(){
    $page = $this->currentPage;
    if ($page == 1){
      $dataMinPage = 1;
    }else{
      $dataMinPage = ($page-1) * $this->limit +1;
    }
    return $dataMinPage;
  }

  // Id of last data in current page
  public function getDataMaxPage(){
    $page = $this->currentPage;
    if ($page == $this->getPages()){
      $dataMaxPage = $this->getDataMax();
    }else{
      $dataMaxPage = $page * $this->limit;
    }
    return $dataMaxPage;
  }

  // Gets Repo according to the page
  public function getData()
  {
    $offset = $this->currentPage * $this->limit - $this->limit;

    if(empty($this->entityClass)){
      if(empty($this->getSpecialRepository())){
        return null;
      }else{
        return $this->manager->createQuery($this->requestEntity)->setMaxResults($this->limit)
        ->setFirstResult($offset)->getResult();
      }
    }else{
      $repo = $this->manager->getRepository($this->entityClass);
    }

    $data = $repo->findBy([], [], $this->limit, $offset);

    return $data;

  }

  // Gets current route
  public function getRoute(){
    return $this->route;
  }

  public function setRoute($route)
  {
    $this->route = $route;

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

  public function getMinPage(){
    if($this->currentPage > 2){
      return $this->currentPage - 2;
    }else{
      return 1;
    }
  }

  public function getMaxPage(){
    $maxPage = $this->currentPage + 2;
    if($this->getPages() > $maxPage){
      return $maxPage;
    }else{
      return $this->getPages();
    }
  }

  // Sends Data to template
  public function display(){
    $this->twig->display($this->templatePath, [
      'min' => $this->getMinPage(),
      'max' => $this->getMaxPage(),
      'page' => $this->currentPage,
      'pages' => $this->getPages(),
      'route' => $this->route,
      'dataMax' => $this->getDataMax(),
      'dataMinPage' => $this->getDataMinPage(),
      'dataMaxPage' => $this->getDataMaxPage(),
    ]);
  }

}