<?php


namespace NatureAndStyle\CoreModule\Core;


class PriceList extends PriceList_parent
{

    public function getVatInfo($isNettoMode = true)
    {
        $aVatValues = [];
        $aPrices = [];
        foreach ($this->_aList as $oPrice) {
            $sKey = (string) $oPrice->getVat();
            if (!isset($aPrices[$sKey])) {
                $aPrices[$sKey]['sum'] = 0;
                $aPrices[$sKey]['vat'] = $oPrice->getVat();
            }
            $aPrices[$sKey]['sum'] += $oPrice->getPrice();
        }

        foreach ($aPrices as $sKey => $aPrice) {
            if ($isNettoMode) {
                $dPrice = $aPrice['sum'] * $aPrice['vat'] / 100;
                $dBruttoPrice = round($aPrice['sum'] + $dPrice,1,PHP_ROUND_HALF_UP);
                $dPrice = $dBruttoPrice-$aPrice['sum'];
            } else {
                $dPrice = $aPrice['sum'] * $aPrice['vat'] / (100 + $aPrice['vat']);
                $dNettoPrice = $aPrice['sum']-$dPrice;
                $dPrice = round($aPrice['sum'], 1 , PHP_ROUND_HALF_UP) - $dNettoPrice;
            }
            $aVatValues[$sKey] = round($dPrice,1,PHP_ROUND_HALF_UP);
        }

        return $aVatValues;
    }

}