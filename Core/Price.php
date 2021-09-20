<?php


namespace NatureAndStyle\CoreModule\Core;


use OxidEsales\Eshop\Core\Registry;

class Price extends Price_parent
{

    public function getVatValue()
    {
        if ($this->isNettoMode()) {
            $logger = Registry::getLogger();
            $dVatValue = $this->getNettoPrice() * $this->getVat() / 100;
            $dBruttoPrice = round($this->getNettoPrice() + $dVatValue, 1, PHP_ROUND_HALF_UP);
            $dVatValue = $dBruttoPrice - $this->getNettoPrice();

            $logger->info($this->getNettoPrice() . " | " . $dVatValue . " | " . $dBruttoPrice);
        } else {
            $dVatValue = $this->getBruttoPrice() * $this->getVat() / (100 + $this->getVat());
        }

        return Registry::getUtils()->fRound($dVatValue);
    }

}