<?php


namespace NatureAndStyle\CoreModule\Application\Controller;


use OxidEsales\Eshop\Core\Registry;

class BasketController extends BasketController_parent
{

    public function getUser()
    {
        $session = Registry::getSession();
        return $session->getBasket()->getBasketUser();
    }

}