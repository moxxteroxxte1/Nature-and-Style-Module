<?php


namespace NatureAndStyle\CoreModule\Application\Controller;

use OxidEsales\Eshop\Core\Registry;

class PaymentController extends PaymentController_parent
{

    public function changeshipping()
    {
        $session = \OxidEsales\Eshop\Core\Registry::getSession();

        $oBasket = $session->getBasket();
        $sShipSet = Registry::getRequest()->getRequestEscapedParameter('sShipSet');
        $oBasket->onUpdate();
        $session->setVariable('sShipSet', $sShipSet);
        $oBasket->setShipping($sShipSet);
        $oBasket->_calcDeliveryCost(false);
    }

}