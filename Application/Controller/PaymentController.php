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
        $oBasket->setSurcharge(false);
        $oBasket->setFindCheapest(false);
        $oBasket->onUpdate();
        $oBasket->setShipping('sShipSet', Registry::getRequest()->getRequestEscapedParameter('sShipSet'));

    }

    public function getAllSets()
    {
        $sActShipId = Registry::getSession()->getBasket()->getShippingId();
        $oActShip = oxNew(DeliverySet::class);
        if ($oActShip->load($sActShipId)) {
            return [$oActShip];
        }

        if ($this->_aAllSets === null) {
            $this->_aAllSets = false;

            if ($this->getPaymentList()) {
                return $this->_aAllSets;
            }
        }

        return $this->_aAllSets;
    }

}