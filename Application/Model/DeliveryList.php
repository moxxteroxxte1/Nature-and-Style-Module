<?php


namespace NatureAndStyle\CoreModule\Application\Model;


use MongoDB\BSON\Regex;
use OxidEsales\Eshop\Application\Model\DeliverySetList;
use OxidEsales\Eshop\Core\Registry;

class DeliveryList extends DeliveryList_parent
{

    protected $blFindCheapest = true;

    public function getDeliveryList($oBasket, $oUser = null, $sDelCountry = null, $sDelSet = null)
    {
        //TODO DEBUG
        $logger = Registry::getLogger();
        //TODO DEBUG END

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
                    $dDeliveryPrice = $oDelivery->getDeliveryPrice($fDelVATPercent)->getPrice();
                    $aUnsortedDeliveries[$dDeliveryPrice] = array('set' => $sDeliverySetId, 'delivery' => $sDeliveryId);

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
                }elseif(!$this->blFindCheapest){
                    Registry::getSession()->setVariable('sShipSet', $sDeliverySetId);
                    return $this->_aDeliveries;
                }
            }
        }

        if($this->blFindCheapest){
            ksort($aUnsortedDeliveries, SORT_NUMERIC);
            $aDeliverySet = array_shift($aUnsortedDeliveries);

            Registry::getSession()->setVariable('sShipSet', $aDeliverySet['set']);
            return $aDeliverySet['delivery'];
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

    private function array_sort($array, $on, $order = SORT_ASC)
    {
        $new_array = array();
        $sortable_array = array();

        if (count($array) > 0) {
            foreach ($array as $k => $v) {
                if (is_array($v)) {
                    foreach ($v as $k2 => $v2) {
                        if ($k2 == $on) {
                            $sortable_array[$k] = $v2;
                        }
                    }
                } else {
                    $sortable_array[$k] = $v;
                }
            }

            switch ($order) {
                case SORT_ASC:
                    asort($sortable_array);
                    break;
                case SORT_DESC:
                    arsort($sortable_array);
                    break;
            }

            foreach ($sortable_array as $k => $v) {
                $new_array[$k] = $array[$k];
            }
        }

        return $new_array;
    }

}