<?php


namespace NatureAndStyle\CoreModule\Application\Model;

use OxidEsales\Eshop\Application\Model\DeliverySetList;
use OxidEsales\Eshop\Core\Registry;

class DeliveryList extends DeliveryList_parent
{
    public function hasDeliveries($oBasket, $oUser, $sDelCountry, $sDeliverySetId)
    {
        $blHas = false;

        // loading delivery list to check if some of them fits
        $this->_getList($oUser, $sDelCountry, $sDeliverySetId);
        foreach ($this as $oDelivery) {
            if ($oDelivery->isForBasket($oBasket)) {
                $blHas = true;
                break;
            }
        }

        return $blHas;
    }

    public function getDeliveryList($oBasket, $oUser = null, $sDelCountry = null, $sDelSet = null, $blFindCheapest = true)
    {
        // ids of deliveries that does not fit for us to skip double check
        $aSkipDeliveries = [];
        $aFittingDelSets = [];
        $aDelSetList = Registry::get(DeliverySetList::class)->getDeliverySetList($oUser, $sDelCountry, $sDelSet);

        $aUnsortedDeliveries = [];
        $fDelVATPercent = $oBasket->getAdditionalServicesVatPercent();

        // must choose right delivery set to use its delivery list
        foreach ($aDelSetList as $sDeliverySetId => $oDeliverySet) {
            // loading delivery list to check if some of them fits
            $aDeliveries = $this->_getList($oUser, $sDelCountry, $sDeliverySetId);
            $blDelFound = false;

            foreach ($aDeliveries as $sDeliveryId => $oDelivery) {
                // skipping that was checked and didn't fit before
                if (in_array($sDeliveryId, $aSkipDeliveries)) {
                    continue;
                }

                $aSkipDeliveries[] = $sDeliveryId;

                if ($oDelivery->isForBasket($oBasket)) {
                    // delivery fits conditions
                    $dDeliveryPrice = $oDelivery->getDeliveryPrice($fDelVATPercent);

                    if($oUser->getIslandSurcharge() > 0 && $oDelivery->oxdelivery__oxhassurcharge->value){
                        $dDeliveryPrice->add($oUser->getIslandSurcharge());
                        $oDelivery->setBlIncludesSurcharge(true);
                    }else{
                        $oDelivery->setBlIncludesSurcharge(false);
                    }

                    $aUnsortedDeliveries[$dDeliveryPrice->getPrice()] = array('set' => $sDeliverySetId, 'delivery' => $aDeliveries[$sDeliveryId], 'surcharge' => $oDelivery->isBlIncludesSurcharge());

                    $this->_aDeliveries[$sDeliveryId] = $aDeliveries[$sDeliveryId];
                    $blDelFound = true;

                    // removing from unfitting list
                    array_pop($aSkipDeliveries);

                    // maybe checked "Stop processing after first match" ?
                    if ($oDelivery->oxdelivery__oxfinalize->value) {
                        break;
                    }
                }
            }

            // found delivery set and deliveries that fits
            if ($blDelFound) {
                if ($this->_blCollectFittingDeliveriesSets) {
                    // collect only deliveries sets that fits deliveries
                    $aFittingDelSets[$sDeliverySetId] = $oDeliverySet;
                } elseif(!$blFindCheapest) {
                    $oBasket->setShipping($sDeliverySetId);
                    return $this->_aDeliveries;
                }
            }
        }

        if ($blFindCheapest && count($aUnsortedDeliveries)) {
            ksort($aUnsortedDeliveries, SORT_NUMERIC);
            $aDeliverySet = array_shift($aUnsortedDeliveries);

            $oBasket->setShipping($aDeliverySet['set']);
            $oBasket->setSurcharge($aDeliverySet['surcharge']);
            return [$aDeliverySet['delivery']];
        }

        if ($this->_blCollectFittingDeliveriesSets && count($aFittingDelSets)) {
            //resetting getting delivery sets list instead of deliveries before return
            $this->_blCollectFittingDeliveriesSets = false;

            //reset cache and list
            $this->setUser(null);
            $this->clear();

            return $aFittingDelSets;
        }

        // nothing what fits was found
        return [];
    }
}