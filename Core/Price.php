<?php


namespace NatureAndStyle\CoreModule\Core;


use OxidEsales\Eshop\Core\Registry;

class Price extends Price_parent
{

    /**
     * @var float|int
     */
    protected $_dBrutto;

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
           $this->getNettoPrice() + $this->getVatValue();
        } else {
            return round(Registry::getUtils()->fRound($this->_dBrutto), 1 , PHP_ROUND_HALF_UP);
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
            $dBruttoPrice = round($this->getNettoPrice() + $dVatValue,1,PHP_ROUND_HALF_UP);
            $dVatValue = $dBruttoPrice-$this->getNettoPrice();
        } else {
            $dVatValue = $this->getBruttoPrice() * $this->getVat() / (100 + $this->getVat());
            $dNettoPrice = $this->getBruttoPrice()-$dVatValue;
            $dVatValue = round($this->getBruttoPrice(), 1 , PHP_ROUND_HALF_UP) - $dNettoPrice;
            $this->_dBrutto = $dNettoPrice+$dVatValue;
        }

        return Registry::getUtils()->fRound($dVatValue);
    }

    public static function netto2Brutto($dNetto, $dVat)
    {
        $dVatValue = $dNetto * $dVat / 100;
        return round($dNetto + $dVatValue,1,PHP_ROUND_HALF_UP);;
    }
}