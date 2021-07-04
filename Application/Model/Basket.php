<?php


namespace NatureAndStyle\CoreModule\Application\Model;


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

                    if($oUser->getIslandSurcharge() > 0){
                        $oDeliveryPrice->add($oUser->getIslandSurcharge());
                        $this->blIncludesSurcharge = true;
                    }

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
}