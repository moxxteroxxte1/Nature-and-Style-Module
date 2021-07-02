<?php


namespace NatureAndStyle\CoreModule\Application\Model;


use OxidEsales\Eshop\Core\Registry;

class DeliveryList extends DeliveryList_parent
{

    public function getDeliveryList($oBasket, $oUser = null, $sDelCountry = null, $sDelSet = null)
    {
        // ids of deliveries that does not fit for us to skip double check
        $aSkipDeliveries = [];
        $aFittingDelSets = [];
        $aDelSetList = Registry::get(\OxidEsales\Eshop\Application\Model\DeliverySetList::class)->getDeliverySetList($oUser, $sDelCountry, $sDelSet);

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
                    array_push($aUnsortedDeliveries, array('price' => $oDelivery->getDeliveryPrice($fDelVATPercent)->getPrice(), 'delivery' => $oDelivery));
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
                } else {
                    // return collected fitting deliveries
                    Registry::getSession()->setVariable('sShipSet', $sDeliverySetId);

                    return array_column(array_sort($aUnsortedDeliveries, 'price', SORT_ASC), 'delivery');
                }
            }
        }

        //return deliveries sets if found
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