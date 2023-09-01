<?php

namespace App\Classe;

use Symfony\Component\HttpFoundation\RequestStack;

class LegalAge {

    private $requestStack;
    private $entityManager;

    /**
     * @var string
     */
    const AGE_KEY_NAME = 'age';

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    public function getAge()
    {
        return $this->requestStack->getSession()->get(self::AGE_KEY_NAME);
    }

    public function setAge($age)
    {
        $this->requestStack->getSession()->set(self::AGE_KEY_NAME, $age);
    }

    public function redirectTo()
    {
        return $this->redirect('https://www.jeunesetalcool.be/');
    }
}