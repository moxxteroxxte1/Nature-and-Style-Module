<?php


namespace NatureAndStyle\CoreModule\Core;


use OxidEsales\Eshop\Core\Registry;

class Price extends Price_parent
{

    /**
     * @var float|int
     */
    private $round = true;
    private $dBruttoOld = null;

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
        $logger = Registry::getLogger();
        if ($this->isNettoMode()) {
            $logger->warning('getNettoPrice', [__CLASS__, __FUNCTION__]);
            return $this->getNettoPrice();
        } else {
            $logger->warning('getBruttoPrice', [__CLASS__, __FUNCTION__]);
            return $this->getBruttoPrice();
        }
    }

    public function getBruttoPrice()
    {
        $logger = Registry::getLogger();
        if ($this->isNettoMode()) {
            $logger->warning('NETTOMODE', [__CLASS__, __FUNCTION__]);
            return $this->getNettoPrice() + $this->getVatValue();
        } elseif ($this->round){
            $logger->warning('BRUTTOMODE + ROUND', [__CLASS__, __FUNCTION__]);
            return round(Registry::getUtils()->fRound($this->_dBrutto), 1, PHP_ROUND_HALF_UP);
        }else{
            $logger->warning('BRUTTOMODE', [__CLASS__, __FUNCTION__]);
            Registry::getUtils()->fRound($this->_dBrutto);
        }
    }

    public function getNettoPrice()
    {
        $logger = Registry::getLogger();
        if ($this->isNettoMode()) {
            $logger->warning('NETTOMODE', [__CLASS__, __FUNCTION__]);
            return Registry::getUtils()->fRound($this->_dNetto);
        } else {
            $logger->warning('BRUTTOMODE', [__CLASS__, __FUNCTION__]);
            return $this->getBruttoPrice() - $this->getVatValue();
        }
    }

    public function getVatValue()
    {
        $logger = Registry::getLogger();
        if ($this->isNettoMode()) {
            $logger->warning('NETTOMODE', [__CLASS__, __FUNCTION__]);
            $dVatValue = Registry::getUtils()->fRound($this->_dNetto) * $this->getVat() / 100;
        } else {
            if($this->round){
                $logger->warning('BRUTTOMODE + ROUND', [__CLASS__, __FUNCTION__]);
                $dBruttoPrice = $this->getBruttoPrice();
                $dVatValue = $dBruttoPrice * $this->getVat() / (100 + $this->getVat());
                $dNettoPrice = $dBruttoPrice - $dVatValue;
                $dVatValue = round($dBruttoPrice, 1, PHP_ROUND_HALF_UP) - $dNettoPrice;
                $this->dBruttoOld = $this->_dBrutto;
                $this->_dBrutto = $dNettoPrice + $dVatValue;
            }else{
                $logger->warning('BRUTTOMODE', [__CLASS__, __FUNCTION__]);
                if($this->dBruttoOld){
                    $logger->warning('dBruttoOld | ' . $this->dBruttoOld  . " | ", [__CLASS__, __FUNCTION__]);
                    $this->_dBrutto = $this->dBruttoOld;
                }
                $logger->warning('BRUTTOMODE', [__CLASS__, __FUNCTION__]);
                $dVatValue = $this->getBruttoPrice() * $this->getVat() / (100 + $this->getVat());
            }
        }

        return Registry::getUtils()->fRound($dVatValue);
    }
}