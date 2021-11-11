<?php


namespace NatureAndStyle\CoreModule\Application\Model;


use mysql_xdevapi\Warning;
use NatureAndStyle\CoreModule\Core\Price;
use NatureAndStyle\CoreModule\Core\PriceList;
use OxidEsales\Eshop\Core\Registry;

class Basket extends Basket_parent
{
    protected $delMulti = 1;
    protected $blFindCheapest = true;
    protected $blIncludesSurcharge = false;
    protected $blIncludesTelAvis = false;

    public function getDeliveryMultiplier()
    {
        return $this->delMulti;
    }

    protected function _calcDeliveryCost()
    {
        if ($this->_oDeliveryPrice !== null) {
            return $this->_oDeliveryPrice;
        }
        $myConfig = Registry::getConfig();
        $oDeliveryPrice = oxNew(Price::class);

        $oUser = $this->getBasketUser();

        if (Registry::getConfig()->getConfigParam('blDeliveryVatOnTop') || ($oUser && $oUser->inGroup('oxiddealer'))) {
            $oDeliveryPrice->setNettoPriceMode();
        } else {
            $oDeliveryPrice->setBruttoPriceMode();
        }

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
                $oDeliverySet = oxNew(DeliverySet::class);
                $dPrice =  $oDeliverySet->load($this->getShippingId()) ? $this->getTelAvisPrice() : 0;

                foreach ($aDeliveryList as $oDelivery) {
                    $dDelPrice = $oDelivery->getDeliveryPrice($fDelVATPercent);
                    if($this->blIncludesTelAvis){
                        $dDelPrice->add($dPrice);
                    }
                    $dDelPrice->add($dPrice);
                    $oDeliveryPrice->addPrice($dDelPrice);
                    $this->blIncludesSurcharge = $oDelivery->isBlIncludesSurcharge();
                    $this->delMulti = $oDelivery->getMultiplier();
                }
            } else {
                return null;
            }
        }

        $this->_oNotDiscountedProductsPriceList->addToPriceList($oDeliveryPrice);

        return $oDeliveryPrice;
    }

    public function setFindCheapest($blFindCheapest = true)
    {
        $this->blFindCheapest = $blFindCheapest;
    }

    public function hasSurcharge()
    {
        return $this->blIncludesSurcharge;
    }

    public function setSurcharge($hasSurcharge)
    {
        $this->blIncludesSurcharge = $hasSurcharge;
    }

    public function getPrice()
    {
        if (is_null($this->_oPrice)) {
            $price = oxNew(Price::class);
            $this->setPrice($price);
        }
        return $this->_oPrice;
    }

    protected function _calcTotalPrice()
    {
        // 1. add products price
        $dPrice = $this->_dBruttoSum;


        /** @var \OxidEsales\Eshop\Core\Price $oTotalPrice */
        $oTotalPrice = oxNew(\OxidEsales\Eshop\Core\Price::class);
        $oTotalPrice->setBruttoPriceMode();
        $oTotalPrice->setRound(false);
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

    public function getGrandTotalNetto()
    {
        $oPrice = oxNew(Price::class);
        $oPrice->setNettoPriceMode();
        $oPrice->add($this->getNettoSum());
        $oPrice->add($this->getDeliveryCost()->getNettoPrice());
        return $oPrice;
    }

    public function hasTelAvis()
    {
        $oDeliverySet = oxNew(DeliverySet::class);
        $oDeliverySet->load($this->getShippingId());

        if ($oDeliverySet) {
            return ($this->getBasketUser()->inGroup('oxiddealer') && $oDeliverySet->hasTelAvis());
        }

        return false;
    }

    public function getTelAvisPrice(){
        $oDeliverySet = oxNew(DeliverySet::class);
        $oDeliverySet->load($this->getShippingId());

        if ($oDeliverySet) {
            return ($oDeliverySet->oxdeliveryset__oxtelavisprice->value);
        }

        return 0;
    }

    public function handleTelAvis($blTelAvis = false)
    {
        $this->blIncludesTelAvis = $blTelAvis;
        $this->_calcDeliveryCost();
    }

    public function isIncludingTelAvis()
    {
        return $this->blIncludesTelAvis;
    }
}