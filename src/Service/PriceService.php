<?php

namespace App\Service;

use App\Classe\Cart;

class PriceService
{
    public function __construct(Cart $cart){
        $this->cart = $cart;
    }

    public function getTotalWeight(){
        $productsWeight = 0;
        foreach($this->cart->getFull() as $product){
            $productsWeight = $productsWeight + ($product['product']->getWeight() * $product['quantity']);
        }
    }

    public function getBoxbyProductWidth(){
        $productsBox = 0;
        foreach($this->cart->getFull() as $product){
            $width = $product['product']->getWidth();
            switch($width) {
                case $width == "S":
                    $box = 0.5;
                    break;
                case $width == "M":
                    $box = 1;
                    break;
                case $width == "L":
                    $box = 1;
                    break;
                default:
                    $box = 1;
                break;
            }
            $productsBox = $productsBox + $box;
        }
        return $productsBox;
    }

    public function getTotalBPOSTCosts(){
        $total = 6.40; // starting cost home delivery 
        $weight = $this->getTotalWeight();
        $weightMin = 0;

        if($weight > 30){
            // return weight rounded (min)
            $extra30 =  intval($weight / 30);
            $weightRounded = 30 * $extra30;
            // return rest
            $weightMin = $weight - $weightRounded;
            // return total by 30 kg increments
            $total = $total + ($extra30 * 8.95);
        }else{
            // return no rest
            $weightMin = 0;
        }

        switch ($weightMin) {
            case $weightMin <= 2:
                $price = 0;
                break;
            case $weightMin <= 5:
                $price = 0.55;
                break;
            case $weightMin <= 10:
                $price = 1.35;
                break;
            case $weightMin<= 20:
                $price = 5.55;
                break;
            default:
                $price = 8.95;
            break;
        }
        
        // add price for weight >30 (extra included)
        $total = $total + $price ;

        //Add transaction Stripe price
        $total = $total + 0.25;

        return $total;
    }

    public function getTotalPriceProduct($price, $tva){
        // TVA Calculation 
        $total = $price + ($price * $tva);
        // Stripe Margin calculation (1,5%)
        $total = $price * 100 / 98.5;
        return $total;
    }

}