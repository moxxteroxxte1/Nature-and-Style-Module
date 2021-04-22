<?php

namespace NatureAndStyle\CoreModule\Application\Model;

class Basket extends Basket_parent
{

    protected function _calcTotalPrice() // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
    {
        // 1. add products price
        $dPrice = $this->_dBruttoSum;


        /** @var \OxidEsales\Eshop\Core\Price $oTotalPrice */
        $oTotalPrice = oxNew(\OxidEsales\Eshop\Core\Price::class);
        $oTotalPrice->setBruttoPriceMode();
        $oTotalPrice->setPrice($dPrice);

        // 2. subtract discounts
        if ($dPrice) {
            if (!$this->isCalculationModeNetto()) {
                $oTotalPrice->subtract($this->_oTotalDiscount->getBruttoPrice());

                if ($oVoucherDisc = $this->getVoucherDiscount()) {
                    $oTotalPrice->subtract($oVoucherDisc->getBruttoPrice());
                }

            } else {
                $oTotalPrice->subtract($this->_oTotalDiscount->getNettoPrice());

                if ($oVoucherDisc = $this->getVoucherDiscount()) {
                    $oTotalPrice->subtract($oVoucherDisc->getNettoPrice());
                }

            }
        }

        // 2.3 add delivery cost
        if (isset($this->_aCosts['oxdelivery'])) {
            $oTotalPrice->add($this->_aCosts['oxdelivery']->getBruttoPrice());
        }

        // 2.4 add wrapping price
        if (isset($this->_aCosts['oxwrapping'])) {
            $oTotalPrice->add($this->_aCosts['oxwrapping']->getBruttoPrice());
        }
        if (isset($this->_aCosts['oxgiftcard'])) {
            $oTotalPrice->add($this->_aCosts['oxgiftcard']->getBruttoPrice());
        }

        // 2.5 add payment price
        if (isset($this->_aCosts['oxpayment'])) {
            $oTotalPrice->add($this->_aCosts['oxpayment']->getBruttoPrice());
        }

        $this->setPrice($oTotalPrice);
    }

}