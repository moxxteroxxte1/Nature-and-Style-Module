<?php


namespace NatureAndStyle\CoreModule\Application\Controller;

use OxdEisales\Eshop\Application\Model\DeliverSetList;
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

    public function getPaymentList()
    {
        $blShippingNull = false;

        if ($this->_oPaymentList === null) {
            $this->_oPaymentList = false;

            $sActShipSet = Registry::getRequest()->getRequestEscapedParameter('sShipSet');
            if (!$sActShipSet) {
                $sActShipSet = Registry::getSession()->getVariable('sShipSet');
                if(is_null($sActShipSet)){
                    $sActShipSet = "74dbcdc315fde44ef79ca43038fe803f";
                    $blShippingNull = true;
                }
            }

            $session = \OxidEsales\Eshop\Core\Registry::getSession();
            $oBasket = $session->getBasket();

            // load sets, active set, and active set payment list
            list($aAllSets, $sActShipSet, $aPaymentList) =
                Registry::get(OxdEisales\Eshop\Application\Model\DeliverySetList::class)->getDeliverySetData($sActShipSet, $this->getUser(), $oBasket);


            if($blShippingNull){
                $sActShipSet = null;
            }
            $oBasket->setShipping($sActShipSet);

            // calculating payment expences for preview for each payment
            $this->setValues($aPaymentList, $oBasket);
            $this->_oPaymentList = $aPaymentList;
            $this->_aAllSets = $aAllSets;
        }

        return $this->_oPaymentList;
    }

}