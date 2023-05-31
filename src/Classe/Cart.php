<?php

namespace App\Classe;

use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;



class Cart {

    private $requestStack;
    private $entityManager;

    /**
     * @var string
     */
    const CART_KEY_NAME = 'cart';

    public function __construct(EntityManagerInterface $entityManager, RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
        $this->entityManager = $entityManager;
    }

    public function getCart()
    {
        return $this->requestStack->getSession()->get(self::CART_KEY_NAME);
    }

    public function add($id)
    {
        $cart = $this->requestStack->getSession()->get(self::CART_KEY_NAME, []);

        if(!empty($cart[$id])){
            $cart[$id]++;   
        }else{
            $cart[$id] = 1;
        }

        $this->requestStack->getSession()->set(self::CART_KEY_NAME, $cart);
    }

    public function remove()
    {
        return $this->requestStack->getSession()->remove(self::CART_KEY_NAME);
    }

    public function delete($id)
    {
        $cart = $this->requestStack->getSession()->get(self::CART_KEY_NAME, []);
        
        unset($cart[$id]);

        return $this->requestStack->getSession()->set(self::CART_KEY_NAME, $cart);
    }

    public function decrease($id)
    {
        $cart = $this->requestStack->getSession()->get(self::CART_KEY_NAME, []);

        if($cart[$id] > 1){
            $cart[$id]--;
        }else{
            unset($cart[$id]);
        }

        return $this->requestStack->getSession()->set(self::CART_KEY_NAME, $cart);
    }

    public function getFull(){
        $cartComplete = [];
 
        if($this->getCart()){
            foreach($this->getCart() as $id => $quantity) {

                $product = $this->entityManager->getRepository(Product::class)->findOneById($id);

                if(!$product){
                    $this->delete($id);
                    continue;
                }

                $cartComplete[] = [
                    'product' => $product,
                    'quantity' => $quantity
                ];
            }
        }

        return $cartComplete;
    }
}