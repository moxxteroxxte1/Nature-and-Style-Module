<?php


namespace NatureAndStyle\CoreModule\Application\Controller;

use OxidEsales\Eshop\Application\Model\DeliverSetList;
use OxidEsales\Eshop\Core\Registry;

class PaymentController extends PaymentController_parent
{

    public function changeshipping()
    {
        $session = Registry::getSession();

        $oBasket = $session->getBasket();
        $oBasket->setShipping(null);
        $oBasket->setFindCheapest(false);
        $oBasket->onUpdate();
        $oBasket->setShipping('sShipSet', Registry::getRequest()->getRequestEscapedParameter('sShipSet'));

    }

    public function getAllSets()
    {
        $logger = Registry::getLogger();
        $sActShip = Registry::getSession()->getBasket()->getShippingId();
        $logger->info($sActShip);
        if($sActShip){
            return $sActShip;
        }
        $this->getPaymentList();
        return $this->_aAllSets;
    }

}