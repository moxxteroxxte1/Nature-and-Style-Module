<?php

namespace NatureAndStyle\CoreModule\Application\Controller;

use OxidEsales\Eshop\Core\Registry;

class OrderController extends OrderController_parent
{

    public function getDeliveryPrice()
    {
        return $this->getBasket()->getDeliveryCost();
    }
}