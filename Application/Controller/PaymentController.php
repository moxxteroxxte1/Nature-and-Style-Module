<?php


namespace NatureAndStyle\CoreModule\Application\Controller;


class PaymentController extends PaymentController_parent
{

    public function changeshipping()
    {
        $session = \OxidEsales\Eshop\Core\Registry::getSession();

        $oBasket = $session->getBasket();
        $oBasket->setShipping(null);
        $oBasket->onUpdate();
        $session->setVariable('sShipSet', Registry::getRequest()->getRequestEscapedParameter('sShipSet'));
        $oBasket->_calcDeliveryCost(false);
    }

}