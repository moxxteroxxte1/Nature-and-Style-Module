<?php


namespace NatureAndStyle\CoreModule\Core;


use OxidEsales\Eshop\Core\Registry;

class Price extends Price_parent
{

    /**
     * @var float|int
     */

    public function getPrice()
    {
        if ($this->isNettoMode()) {
            return $this->getNettoPrice();
        } else {
            return $this->getBruttoPrice();
        }
    }

    public function getBruttoPrice()
    {
        if ($this->isNettoMode()) {
            return $this->getNettoPrice() + $this->getVatValue();
        } else {
            return round(Registry::getUtils()->fRound($this->_dBrutto), 1, PHP_ROUND_HALF_UP);
        }
    }

    public function getNettoPrice()
    {
        if ($this->isNettoMode()) {
            $this->getVatValue();
            return Registry::getUtils()->fRound($this->_dNetto);
        } else {
            return $this->getBruttoPrice() - $this->getVatValue();
        }
    }

    public function getVatValue()
    {
        $logger = Registry::getLogger();
        if ($this->isNettoMode()) {
            $dNettoPrice =$this->getNettoPrice();
            $dVatValue = $dNettoPrice * $this->getVat() / 100;

            $dBruttoPrice = round($dNettoPrice + $dVatValue, 1, PHP_ROUND_HALF_UP);
            $dVatValue = $dBruttoPrice * $this->getVat() / (100 + $this->getVat());
            $dNettoPrice = $dBruttoPrice - $dVatValue;
            $dVatValue = round($dBruttoPrice, 1, PHP_ROUND_HALF_UP) - $dNettoPrice;
            $this->_dNetto = $dNettoPrice;

        } else {
            $dBruttoPrice = $this->getBruttoPrice();
            $dVatValue = $dBruttoPrice * $this->getVat() / (100 + $this->getVat());
            $dNettoPrice = $dBruttoPrice - $dVatValue;
            $dVatValue = round($dBruttoPrice, 1, PHP_ROUND_HALF_UP) - $dNettoPrice;
            $this->_dBrutto = $dNettoPrice + $dVatValue;
        }

        return Registry::getUtils()->fRound($dVatValue);
    }

    public static function netto2Brutto($dNetto, $dVat)
    {
        $dVatValue = $dNetto * $dVat / 100;
        return round($dNetto + $dVatValue, 1, PHP_ROUND_HALF_UP);
    }
}