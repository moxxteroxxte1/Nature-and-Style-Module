<?php


namespace NatureAndStyle\CoreModule\Core;


use OxidEsales\Eshop\Core\Registry;

class Price extends Price_parent
{

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
            return \OxidEsales\Eshop\Core\Registry::getUtils()->fRound($this->_dBrutto);
        }
    }

    public function getNettoPrice()
    {
        if ($this->isNettoMode()) {
            return \OxidEsales\Eshop\Core\Registry::getUtils()->fRound($this->_dNetto);
        } else {
            return $this->getBruttoPrice() - $this->getVatValue();
        }
    }

    public function getVatValue()
    {
        if ($this->isNettoMode()) {
            $dVatValue = $this->getNettoPrice() * $this->getVat() / 100;
        } else {
            $dVatValue = $this->getBruttoPrice() * $this->getVat() / (100 + $this->getVat());
        }

        /*$logger = Registry::getLogger();
        $dBruttoPrice = round($this->getNettoPrice() + $dVatValue, 1, PHP_ROUND_HALF_UP);
        $dVatValue = $dBruttoPrice - $this->getNettoPrice();
        $logger->info($this->getNettoPrice() . " | " . $dVatValue . " | " . $dBruttoPrice);*/

        return \OxidEsales\Eshop\Core\Registry::getUtils()->fRound($dVatValue);
    }

}