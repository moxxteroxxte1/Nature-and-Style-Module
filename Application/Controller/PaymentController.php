<?php


namespace NatureAndStyle\CoreModule\Application\Controller;

use OxidEsales\Eshop\Core\Registry;

class PaymentController extends PaymentController_parent
{

    public function changeshipping()
    {
        $session = \OxidEsales\Eshop\Core\Registry::getSession();

        $oBasket = $session->getBasket();
        $oBasket->setShipping(null);
        $oBasket->setFindCheapest(false);
        $oBasket->onUpdate();
        $session->setVariable('sShipSet', Registry::getRequest()->getRequestEscapedParameter('sShipSet'));

    }

}