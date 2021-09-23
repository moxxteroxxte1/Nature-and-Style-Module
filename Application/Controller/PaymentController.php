<?php


namespace NatureAndStyle\CoreModule\Application\Controller;

use OxidEsales\Eshop\Application\Model\DeliverSetList;
use OxidEsales\Eshop\Application\Model\DeliverySet;
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
        $sActShipId = Registry::getSession()->getBasket()->getShippingId();
        $oActShip = oxNew(DeliverySet::class);
        if ($oActShip->load($sActShipId)) {
            $logger->error("2");
            $logger->error($sActShipId);
            return array($oActShip);
        }

        if ($this->_aAllSets === null) {
            $logger->error("1");
            $this->_aAllSets = false;

            if ($this->getPaymentList()) {
                $logger->error("3");
                return $this->_aAllSets;
            }
        }
        $logger->error("4");
        return $this->_aAllSets;

    }

}