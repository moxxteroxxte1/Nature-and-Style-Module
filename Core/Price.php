<?php

namespace NatureAndStyle\CoreModule\Core;

class Price extends Price_parent
{
    public function getVatValue()
    {
        if ($this->isNettoMode()) {
            $dVatValue = $this->getNettoPrice() * $this->getVat() / 100;
            $dVatValue = (ceil($dVatValue*100)/100);
        } else {
            $dVatValue = $this->getBruttoPrice() * $this->getVat() / (100 + $this->getVat());
        }
        return  \OxidEsales\Eshop\Core\Registry::getUtils()->fRound($dVatValue);
    }

    public function getBruttoPrice()
    {
        return \OxidEsales\Eshop\Core\Registry::getUtils()->fRound($this->_dBrutto);
    }

    public function getNettoPrice()
    {
        if ($this->isNettoMode()) {
            return \OxidEsales\Eshop\Core\Registry::getUtils()->fRound($this->_dNetto);
        } else {
            return $this->getBruttoPrice() - $this->getVatValue();
        }
    }

     public function setPrice($dPrice, $dVat = null)
        {
            if (!is_null($dVat)) {
                $this->setVat($dVat);
            }

            if ($this->isNettoMode()) {
                $dVatValue = ($dPrice * $this->getVat()) / 100;
                $dVatValue = (ceil($dVatValue*100)/100);

                $dBrutto = $dPrice + $dVatValue;
                $dBrutto = round($dBrutto,2,PHP_ROUND_HALF_UP);
                $this->_dNetto = ($dBrutto-$dVatValue);
                $this->_dBrutto = $dBrutto;
            } else {
                $this->_dBrutto = $dPrice;
            }
        }
}

