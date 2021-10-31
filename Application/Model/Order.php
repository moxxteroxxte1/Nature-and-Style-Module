<?php

namespace NatureAndStyle\CoreModule\Application\Model;

use OxidEsales\Eshop\Core\Registry;

class Order extends Order_parent
{
    public function validateDelivery($oBasket)
    {
        if(Registry::getSession()->getVariable('hasNoShipSet')){
            return;
        }
        return parent::validateDelivery;
    }

}