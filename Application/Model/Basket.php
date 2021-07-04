<?php


namespace NatureAndStyle\CoreModule\Application\Model;


use OxidEsales\Eshop\Core\Price;
use OxidEsales\Eshop\Core\Registry;

class Basket extends Basket_parent
{
    protected $delMulti = 1;

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
                $this->getShippingId()
            );

            if (count($aDeliveryList) > 0) {
                foreach ($aDeliveryList as $oDelivery) {
                    $oDeliveryPrice->addPrice($oDelivery->getDeliveryPrice($fDelVATPercent));
                    $this->delMulti += $oDelivery->getMultiplier();
                }
            }
        }

        return $oDeliveryPrice;
    }
}