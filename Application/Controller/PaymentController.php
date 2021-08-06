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

    public function getPaymentList()
    {
        if ($this->_oPaymentList === null) {
            $this->_oPaymentList = false;

            $sActShipSet = Registry::getRequest()->getRequestEscapedParameter('sShipSet');
            if (!$sActShipSet) {
                $sActShipSet = Registry::getSession()->getVariable('sShipSet');
            }

            $session = \OxidEsales\Eshop\Core\Registry::getSession();
            $oBasket = $session->getBasket();

            // load sets, active set, and active set payment list
            list($aAllSets, $sActShipSet, $aPaymentList) =
               Registry::get(DeliverySetList::class)->getDeliverySetData($sActShipSet, $this->getUser(), $oBasket);

            $oBasket->setShipping($sActShipSet);

            // calculating payment expences for preview for each payment
            $this->setValues($aPaymentList, $oBasket);
            $this->_oPaymentList = $aPaymentList;

            $user = $this->getUser();
            $oxDeliverySetList = oxNew('oxdeliverysetlist');
            $aActiveSets = $oxDeliverySetList->getActiveDeliverySetList($user, $user->getUserCountry());#

            $this->_aAllSets = $aActiveSets;
        }

        return $this->_oPaymentList;
    }

}