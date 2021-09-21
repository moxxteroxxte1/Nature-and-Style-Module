<?php


namespace NatureAndStyle\CoreModule\Application\Model;


use NatureAndStyle\CoreModule\Core\PriceList;
use OxidEsales\Eshop\Core\Price;
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
            $price = oxNew(\OxidEsales\Eshop\Core\Price::class);
            $price->setNettoMode($this->isPriceViewModeNetto());
            $this->setPrice($price);
        }

        return $this->_oPrice;
    }

}