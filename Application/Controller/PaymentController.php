<?php


namespace NatureAndStyle\CoreModule\Application\Controller;

use NatureAndStyle\CoreModule\Application\Model\DeliverySetList;
use OxidEsales\Eshop\Application\Model\DeliverySet;
use OxidEsales\Eshop\Application\Model\Payment;
use OxidEsales\Eshop\Application\Model\PaymentList;
use OxidEsales\Eshop\Core\Registry;

class PaymentController extends PaymentController_parent
{
    var $sShipSet = "74dbcdc315fde44ef79ca43038fe803f";

    public function changeshipping()
    {
        $session = Registry::getSession();

        $oBasket = $session->getBasket();
        /*$oBasket->setShipping(null);
        $oBasket->setSurcharge(false);
        $oBasket->setFindCheapest(false);
        $oBasket->onUpdate();
        $oBasket->setShipping('sShipSet', Registry::getRequest()->getRequestEscapedParameter('sShipSet'));*/
        $oBasket->handleTelAvis(boolval(Registry::getRequest()->getRequestEscapedParameter('blTelAvis')));
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
            }

            $session = Registry::getSession();
            $session->setVariable("hasNoShipSet", false);
            $oBasket = $session->getBasket();

            // load sets, active set, and active set payment list
            list($aAllSets, $sActShipSet, $aPaymentList) =
                Registry::get(DeliverySetList::class)->getDeliverySetData($sActShipSet, $this->getUser(), $oBasket);

            if (empty($aPaymentList) || !$aPaymentList) {
                $dPrice = $oBasket->getPrice()->getPrice();
                $session->setVariable("hasNoShipSet", true);
                $aPaymentList = oxNew(PaymentList::class)->getPaymentList($this->sShipSet, $dPrice, $this->getUser());
            }

            $oBasket->setShipping($sActShipSet);

            // calculating payment expences for preview for each payment
            $this->setValues($aPaymentList, $oBasket);
            $this->_oPaymentList = $aPaymentList;
            $this->_aAllSets = $aAllSets;
        }

        return $this->_oPaymentList;
    }

    public function validatePayment()
    {
        $myConfig = Registry::getConfig();
        $session = Registry::getSession();

        //#1308C - check user. Function is executed before render(), and oUser is not set!
        // Set it manually for use in methods getPaymentList(), getShippingSetList()...
        $oUser = $this->getUser();
        if (!$oUser) {
            $session->setVariable('payerror', 2);
            return;
        }

        if (!($sShipSetId = Registry::getRequest()->getRequestEscapedParameter('sShipSet'))) {
            $sShipSetId = ($session->getVariable('hasNoShipSet') ? $this->sShipSet : $session->getVariable('sShipSet'));
        }
        if (!($sPaymentId = Registry::getRequest()->getRequestEscapedParameter('paymentid'))) {
            $sPaymentId = $session->getVariable('paymentid');
        }
        if (!($aDynvalue = Registry::getRequest()->getRequestEscapedParameter('dynvalue'))) {
            $aDynvalue = $session->getVariable('dynvalue');
        }

        // A. additional protection
        if (!$myConfig->getConfigParam('blOtherCountryOrder') && $sPaymentId == 'oxempty') {
            $sPaymentId = '';
        }

        //#1308C - check if we have paymentID, and it really exists
        if (!$sPaymentId) {
            $session->setVariable('payerror', 1);

            return;
        }

        $oBasket = $session->getBasket();
        $oBasket->setPayment(null);
        $oPayment = oxNew(Payment::class);
        $oPayment->load($sPaymentId);

        // getting basket price for payment calculation
        $dBasketPrice = $oBasket->getPriceForPayment();

        $blOK = $oPayment->isValidPayment($aDynvalue, $myConfig->getShopId(), $oUser, $dBasketPrice, $sShipSetId);

        if ($blOK) {
            $session->setVariable('paymentid', $sPaymentId);
            $session->setVariable('dynvalue', $aDynvalue);
            $oBasket->setShipping(($session->getVariable('hasNoShipSet') ? null : $sShipSetId));
            $session->deleteVariable('_selected_paymentid');
            return 'order';
        } else {
            $session->setVariable('payerror', $oPayment->getPaymentErrorNumber());

            //#1308C - delete paymentid from session, and save selected it just for view
            $session->deleteVariable('paymentid');
            $session->setVariable('_selected_paymentid', $sPaymentId);

            return;
        }
    }

    protected function setValues($aPaymentList, $oBasket = null)
    {
        if (is_array($aPaymentList)) {
            foreach ($aPaymentList as $oPayment) {
                $oPayment->calculate($oBasket);
                $oPayment->aDynValues = $oPayment->getDynValues();
                if ($oPayment->oxpayments__oxchecked->value) {
                    $this->_sCheckedId = $oPayment->getId();
                }
            }
        }
    }

}