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
            return Registry::getUtils()->fRound($this->_dNetto);
        } else {
            return $this->getBruttoPrice() - $this->getVatValue();
        }
    }

    public function getVatValue()
    {
        $logger = Registry::getLogger();
        if ($this->isNettoMode()) {
            $logger->info("---NETTOMODE---");
            $dNettoPrice = Registry::getUtils()->fRound($this->_dNetto);
            $logger->info("NettoPrice: " . $dNettoPrice);
            $dVatValue = $dNettoPrice * $this->getVat() / 100;
            $logger->info("VatValue: " . $dVatValue);
            $dBruttoPrice = round($dNettoPrice + $dVatValue, 1, PHP_ROUND_HALF_UP);
            $logger->info("BruttoPrice: " . $dBruttoPrice);
            $dVatValue = $dBruttoPrice - $dNettoPrice;
            $logger->info("VatValue: " . $dVatValue);
            $this->_dBrutto = $dBruttoPrice;
            $logger->info("---------------");
        } else {
            $logger->info("---BRUTTOMODE---");
            $dBruttoPrice = Registry::getUtils()->fRound($this->_dBrutto);
            $logger->info("BruttoPrice: " . $dBruttoPrice);
            $dVatValue = $dBruttoPrice * $this->getVat() / (100 + $this->getVat());
            $logger->info("VatValue: " . $dVatValue);
            $dNettoPrice = $dBruttoPrice - $dVatValue;
            $dNettoPrice = Registry::getUtils()->fRound($this->_dNetto);
            $dVatValue = round($dBruttoPrice, 1, PHP_ROUND_HALF_UP) - $dNettoPrice;
            $logger->info("VatValue: " . $dVatValue);
            $this->_dBrutto = $dNettoPrice + $dVatValue;
            $this->_dNetto = $dNettoPrice;
            $logger->info("---------------");
        }

        return Registry::getUtils()->fRound($dVatValue);
    }

    public static function netto2Brutto($dNetto, $dVat)
    {
        $dVatValue = $dNetto * $dVat / 100;
        return round($dNetto + $dVatValue, 1, PHP_ROUND_HALF_UP);
    }
}