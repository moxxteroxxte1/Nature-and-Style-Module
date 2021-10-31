<?php

namespace NatureAndStyle\CoreModule\Application\Model;

use OxidEsales\Eshop\Core\Registry;

class Order extends Order_parent
{
    public function validateDelivery($oBasket)
    {
        if(Registry::getSession()->getVariable('hasNoShipSet')){
            Registry::getLogger()->error("noShipSet");
            return;
        }
        return parent::validateDelivery;
    }

}