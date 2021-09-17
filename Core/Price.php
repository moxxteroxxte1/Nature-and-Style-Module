<?php


namespace NatureAndStyle\CoreModule\Core;


class Price extends Price_parent
{

    public function getPrice()
    {
        if($this->isNettoMode()){
            return $this->getNettoPrice();
        }else{
            return $this->getBruttoPrice();
        }
    }

    /**
     * Returns brutto price
     *
     * @return double
     */
    public function getBruttoPrice()
    {
        if ($this->isNettoMode()) {
            return \OxidEsales\Eshop\Core\Registry::getUtils()->fRound($this->getNettoPrice() + $this->getVatValue());
        } else {
            return \OxidEsales\Eshop\Core\Registry::getUtils()->fRound($this->_dBrutto);
        }
    }

    /**
     * Returns netto price
     *
     * @return double
     */
    public function getNettoPrice()
    {
        if ($this->isNettoMode()) {
            return \OxidEsales\Eshop\Core\Registry::getUtils()->fRound($this->_dNetto);
        } else {
            return $this->getBruttoPrice() - $this->getVatValue();
        }
    }

}