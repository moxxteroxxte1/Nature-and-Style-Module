<?php


namespace NatureAndStyle\CoreModule\Core;


use OxidEsales\Eshop\Core\Registry;

class Price extends Price_parent
{

    /**
     * @var float|int
     */
    private $round = true;
    private $dBruttoOld;

    public function setRound($round = true)
    {
        $this->round = $round;
    }

    public function getRount()
    {
        return $this->round;
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
        } elseif ($this->round){
            return round(Registry::getUtils()->fRound($this->_dBrutto), 1, PHP_ROUND_HALF_UP);
        }else{
            Registry::getUtils()->fRound($this->_dBrutto);
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
            $dVatValue = Registry::getUtils()->fRound($this->_dNetto) * $this->getVat() / 100;
        } else {
            if($this->round){
                $dBruttoPrice = $this->getBruttoPrice();
                $dVatValue = $dBruttoPrice * $this->getVat() / (100 + $this->getVat());
                $dNettoPrice = $dBruttoPrice - $dVatValue;
                $dVatValue = round($dBruttoPrice, 1, PHP_ROUND_HALF_UP) - $dNettoPrice;
                $this->dBruttoOld = $this->_dBrutto;
                $this->_dBrutto = $dNettoPrice + $dVatValue;
            }else{
                if($this->dBruttoOld){
                    $this->_dBrutto = $this->dBruttoOld;
                }
                $dVatValue = $this->getBruttoPrice() * $this->getVat() / (100 + $this->getVat());
            }
        }

        return Registry::getUtils()->fRound($dVatValue);
    }
}