<?php


namespace NatureAndStyle\CoreModule\Core;


use OxidEsales\Eshop\Core\Registry;

class Price extends Price_parent
{

    /**
     * @var float|int
     */

    private $round = true;

    /**
     * @return float|int
     */
    public function getRound()
    {
        return $this->round;
    }

    /**
     * @param float|int $round
     */
    public function setRound($round): void
    {
        $this->round = $round;
    }

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
            return ($this->round ? round(Registry::getUtils()->fRound($this->_dBrutto), 1, PHP_ROUND_HALF_UP) : Registry::getUtils()->fRound($this->_dBrutto));
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
        if ($this->isNettoMode()) {
            $dVatValue = Registry::getUtils()->fRound($this->_dNetto) * $this->getVat() / 100;
        } else {
            if ($this->round) {
                $dBruttoPrice = $this->getBruttoPrice();
                $dVatValue = $dBruttoPrice * $this->getVat() / (100 + $this->getVat());
                $dNettoPrice = $dBruttoPrice - $dVatValue;
                $dVatValue = round($dBruttoPrice, 1, PHP_ROUND_HALF_UP) - $dNettoPrice;
                $this->_dBrutto = $dNettoPrice + $dVatValue;
            }else{
                $dVatValue = $this->getBruttoPrice() * $this->getVat() / (100 + $this->getVat());
            }
        }

        return Registry::getUtils()->fRound($dVatValue);
    }
}