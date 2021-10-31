<?php

namespace NatureAndStyle\CoreModule\Application\Model;

use OxidEsales\Eshop\Core\Registry;

class Order extends Order_parent
{
    public function validateDelivery($oBasket)
    {
        if(Registry::getSession()->getVariable('hasNoShipSet')){
            return true;
        }
        return parent::validateDelivery;
    }

    public function validateOrder($oBasket, $oUser)
    {
        // validating stock
        $iValidState = $this->validateStock($oBasket);

        if (!$iValidState) {
            // validating delivery
            $iValidState = $this->validateDelivery($oBasket);
        }

        if (!$iValidState) {
            // validating payment
            $iValidState = $this->validatePayment($oBasket, $oUser);
        }

        if (!$iValidState) {
            //0003110 validating delivery address, it is not be changed during checkout process
            $iValidState = $this->validateDeliveryAddress($oUser);
        }

        if (!$iValidState) {
            // validating minimum price
            $iValidState = $this->validateBasket($oBasket);
        }

        if (!$iValidState) {
            // validating vouchers
            $iValidState = $this->validateVouchers($oBasket);
        }

        return $iValidState;
    }
}