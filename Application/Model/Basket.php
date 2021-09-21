<?php


namespace NatureAndStyle\CoreModule\Application\Model;


use NatureAndStyle\CoreModule\Core\Price;
use NatureAndStyle\CoreModule\Core\PriceList;
use OxidEsales\Eshop\Core\Registry;

class Basket extends Basket_parent
{
    protected $delMulti = 1;
    protected $blFindCheapest = true;
    protected $blIncludesSurcharge = false;

    public function getDeliveryMultiplier(){
        return $this->delMulti;
    }

    protected function _calcDeliveryCost()
    {
        if ($this->_oDeliveryPrice !== null) {
            return $this->_oDeliveryPrice;
        }
        $myConfig = Registry::getConfig();
        $oDeliveryPrice = oxNew(Price::class);

        if (Registry::getConfig()->getConfigParam('blDeliveryVatOnTop')) {
            $oDeliveryPrice->setNettoPriceMode();
        } else {
            $oDeliveryPrice->setBruttoPriceMode();
        }

        // don't calculate if not logged in
        $oUser = $this->getBasketUser();

        if (!$oUser && !$myConfig->getConfigParam('blCalculateDelCostIfNotLoggedIn')) {
            return $oDeliveryPrice;
        }

        $fDelVATPercent = $this->getAdditionalServicesVatPercent();
        $oDeliveryPrice->setVat($fDelVATPercent);

        // list of active delivery costs
        if ($myConfig->getConfigParam('bl_perfLoadDelivery')) {
            $aDeliveryList = Registry::get(DeliveryList::class)->getDeliveryList(
                $this,
                $oUser,
                $this->_findDelivCountry(),
                $this->getShippingId(),
                $this->blFindCheapest
            );

            if (count($aDeliveryList) > 0) {
                foreach ($aDeliveryList as $oDelivery) {
                    $oDeliveryPrice->addPrice($oDelivery->getDeliveryPrice($fDelVATPercent));
                    $this->blIncludesSurcharge = $oDelivery->isBlIncludesSurcharge();
                    $this->delMulti = $oDelivery->getMultiplier();
                }
            }
        }

        return $oDeliveryPrice;
    }

    public function setFindCheapest($blFindCheapest = true){
        $this->blFindCheapest = $blFindCheapest;
    }

    public function hasSurcharge(){
        return $this->blIncludesSurcharge;
    }

    public function getPrice()
    {
        if (is_null($this->_oPrice)) {
            /** @var \OxidEsales\Eshop\Core\Price $price */
            $price = oxNew(Price::class);
            $price->setRound(!$this->isCalculationModeNetto());
            $this->setPrice($price);
        }
        $this->_oPrice->setRound(!$this->isCalculationModeNetto());
        $this->_oPrice->getVatValue();
        return $this->_oPrice;
    }

    protected function calcTotalPrice()
    {
        // 1. add products price
        $dPrice = $this->_dBruttoSum;

        /** @var \OxidEsales\Eshop\Core\Price $oTotalPrice */
        $oTotalPrice = oxNew(Price::class);
        $oTotalPrice->setRound(!$this->isCalculationModeNetto());
        $oTotalPrice->setBruttoMode();
        $oTotalPrice->setPrice($dPrice);


        // 2. subtract discounts
        if ($dPrice && !$this->isCalculationModeNetto()) {
            // 2.2 applying basket discounts
            $oTotalPrice->subtract($this->_oTotalDiscount->getBruttoPrice());

            // 2.3 applying voucher discounts
            if ($oVoucherDisc = $this->getVoucherDiscount()) {
                $oTotalPrice->subtract($oVoucherDisc->getBruttoPrice());
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