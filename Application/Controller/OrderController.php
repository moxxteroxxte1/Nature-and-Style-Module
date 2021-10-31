<?php

namespace NatureAndStyle\CoreModule\Application\Controller;

use OxidEsales\Eshop\Core\Registry;

class OrderController extends OrderController_parent
{
    var $sShipSet = "74dbcdc315fde44ef79ca43038fe803f";

    public function getDeliveryPrice()
    {
        return $this->getBasket()->getDeliveryCost();
    }

    public function getPayment()
    {
        if ($this->_oPayment === null) {
            $this->_oPayment = false;

            $oBasket = $this->getBasket();
            $oUser = $this->getUser();

            // payment is set ?
            $sPaymentid = $oBasket->getPaymentId();
            $oPayment = oxNew(\OxidEsales\Eshop\Application\Model\Payment::class);

            $session = Registry::getSession();

            if (
                $sPaymentid && $oPayment->load($sPaymentid) &&
                $oPayment->isValidPayment(
                    Registry::getSession()->getVariable('dynvalue'),
                    Registry::getConfig()->getShopId(),
                    $oUser,
                    $oBasket->getPriceForPayment(),
                    ($session->getVariable('hasNoShipSet') ? $this->sShipSet : $session->getVariable('sShipSet'))
                )
            ) {
                $this->_oPayment = $oPayment;
            }
        }

        return $this->_oPayment;
    }
}