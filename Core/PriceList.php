<?php


namespace NatureAndStyle\CoreModule\Core;

use OxidEsales\Eshop\Core\Registry;

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
                $logger = Registry::getLogger();
                $dPrice = $aPrice['sum'] * $aPrice['vat'] / 100;
                $dBruttoPrice = round($aPrice['sum'] + $dPrice,1,PHP_ROUND_HALF_UP);
                $dPrice = $dBruttoPrice-$aPrice['sum'];
                $logger->info($dPrice . " | " . $dBruttoPrice . " | " . $aPrice['sum']);
            } else {
                $dPrice = $aPrice['sum'] * $aPrice['vat'] / (100 + $aPrice['vat']);
                $dNettoPrice = $aPrice['sum']-$dPrice;
                $dPrice = round($aPrice['sum'], 1 , PHP_ROUND_HALF_UP) - $dNettoPrice;
            }
            $aVatValues[$sKey] = $dPrice;
        }

        return $aVatValues;
    }

    public function calculateToPrice()
    {
        if (count($this->_aList) == 0) {
            return;
        }

        $dNetoTotal = 0;
        $dVatTotal = 0;

        foreach ($this->_aList as $oPrice) {
            $dNetoTotal += $oPrice->getNettoPrice();
            $dVatTotal += $oPrice->getVatValue();
        }

        $oPrice = oxNew(\OxidEsales\Eshop\Core\Price::class);

        if ($dNetoTotal) {
            $dVat = $dVatTotal * 100 / $dNetoTotal;

            $oPrice->setNettoPriceMode();
            $oPrice->setPrice($dNetoTotal);
            $oPrice->setVat($dVat);
        }

        return $oPrice;
    }

}